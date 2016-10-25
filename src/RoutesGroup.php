<?php


namespace Laasti\Directions;

class RoutesGroup
{
    use Conditions\ConditionableTrait;
    
    protected $strategy;
    protected $suffix = '';
    protected $prefix = '';
    protected $host;
    protected $scheme;
    protected $middlewares = [];
    protected $routes = [];

    public function __construct(Strategies\StrategyInterface $strategy, $prefix = null, $suffix = null, $domain = null, $scheme = null)
    {
        $this->strategy = $strategy;
        $this->suffix = $suffix;
        $this->prefix = $prefix;
        $this->host = $domain;
        $this->scheme = $scheme;
    }

    public function add($httpMethod, $uri, $handler, $middlewares = [])
    {
        $route = new Route($httpMethod, $uri, $handler, $this->strategy);
        $route->setMiddlewares($middlewares);
        $route->setGroup($this);
        $this->routes[] = $route;
        return $route;
    }

    public function pushMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }


    public function getSuffix()
    {
        return $this->suffix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

}
