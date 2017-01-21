<?php

namespace Laasti\Directions;

use BadMethodCallException;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class UrlBuilder
{
    protected $request;
    protected $routes;

    public function __construct(ServerRequestInterface $request, RouteCollection $routes = null)
    {
        $this->request = $request;
        $this->routes = $routes;
    }

    public function getCurrentUri($host = false, $includeQueryParams = false)
    {
        if ($includeQueryParams) {
            return (string)$this->request->getUri();
        }

        $url = '';
        if ($host) {
            $url .= $this->getHost();
        }
        return rtrim($url, '/') . '/' . ltrim($this->request->getUri()->getPath(), '/');
    }

    public function getHost()
    {
        $uri = $this->request->getUri();
        return sprintf('%s://%s/', $uri->getScheme(), $uri->getAuthority());
    }

    public function createByName($name, $params = [], $host = false)
    {
        if (is_null($this->routes)) {
            throw new BadMethodCallException('You must provide a Laasti\Directions\RouteCollection to the builder to use this method.');
        }

        $route = $this->routes->getRouteByName($name);

        return $this->create($route->getRoute(), $params, $host);
    }

    public function create($format, $params = [], $host = false)
    {
        return rtrim($this->getBaseUri($host), '/') . '/' . ltrim($this->parseUri($format, $params), '/');
    }

    public function getBaseUri($host = false)
    {
        $server = $this->request->getServerParams();
        $folder = '';
        if (isset($server['SCRIPT_NAME'])) {
            $folder = pathinfo($server['SCRIPT_NAME'], PATHINFO_DIRNAME);
        } elseif (isset($server['PHP_SELF'])) {
            $folder = pathinfo($server['PHP_SELF'], PATHINFO_DIRNAME);
        }
        $folder = str_replace('\\', '/', $folder);
        if ($host) {
            $host = $this->getHost();
        }

        return $host ? $host . ltrim($folder, '/') : $folder;
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
}
