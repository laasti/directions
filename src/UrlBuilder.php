<?php

namespace Laasti\Directions;

use Exception;
use Psr\Http\Message\ServerRequestInterface;

class UrlBuilder
{
    protected $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function getCurrentUri($host = false, $complete = false)
    {
        if ($complete) {
            return (string) $this->request->getUri();
        }

        $url = '';
        if ($host) {
            $url .= $this->getHost();
        }
        return rtrim($url, '/').'/'.ltrim($this->request->getUri()->getPath(), '/');
    }
    
    public function create($format, $params = [], $host = false)
    {
        return rtrim($this->getBaseUri($host), '/').'/'.ltrim($this->parseUri($format, $params), '/');
    }

    public function parseUri($uri, $params = [])
    {
        $regex = '#\{([0-9a-z_]+):?[a-z]*\}+#';
        $matches = [];
        $params = array_merge($this->request->getAttributes(), $params);
        if (preg_match_all($regex, $uri, $matches)) {
            foreach ($matches[1] as $key => $attr) {
                if (!isset($params[$attr])) {
                    throw new Exception('Attribute used to generate URLs does not exist: ' . $attr);
                } else {
                    $uri = str_replace($matches[0][$key], $params[$attr], $uri);
                }
            }
        }

        return $uri;
    }

    public function getHost()
    {
        $uri = $this->request->getUri();
        return sprintf('%s://%s/', $uri->getScheme(), $uri->getAuthority());
    }

    public function getBaseUri($host = false)
    {
        $server = $this->request->getServerParams();
        $folder = '';
        if (isset($server['SCRIPT_NAME'])) {
            $folder = pathinfo($server['SCRIPT_NAME'], PATHINFO_DIRNAME);
        } else if (isset($server['PHP_SELF'])) {
            $folder = pathinfo($server['PHP_SELF'], PATHINFO_DIRNAME);
        }

        if ($host) {
            $host = $this->getHost();
        }
        
        return $host ? $host.ltrim($folder, '/') : $folder;
    }

}
