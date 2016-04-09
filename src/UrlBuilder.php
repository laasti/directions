<?php

namespace Laasti\Directions;

class UrlBuilder
{
    protected $request;

    public function __construct(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $this->request = $request;
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
            $uri = $this->request->getUri();
            $host = sprintf('%s://', $uri->getScheme(), $uri->getAuthority());
        }
        return $host.$folder;
    }

}
