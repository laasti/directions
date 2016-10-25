<?php


namespace Laasti\Directions;

use FastRoute\DataGenerator\GroupCountBased as GroupCountBased2;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Laasti\Directions\Conditions\ManyRoutesFoundException;
use Laasti\Directions\Conditions\RouteConditionFailedException;
use Laasti\Directions\Exceptions\MethodNotAllowedException;
use Laasti\Directions\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class Locator extends GroupCountBased
{
    
    protected $routes;
    protected $routesIndex = [];
    
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }
    
    public function find($httpMethod, $uri, ServerRequestInterface $request)
    {
        list($this->staticRouteMap, $this->variableRouteData) = $this->getCollector()->getData();
        $result = parent::dispatch($httpMethod, $uri);
        
        switch ($result[0]) {
            case self::NOT_FOUND:
                throw new RouteNotFoundException;
            case self::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException((array) $result[1]);
        }

        $route = $this->getRoute($result[1], $request);
        
        foreach ((array)$result[2] as $name => $value) {
            $route->setAttribute($name, $value);
        } 
        
        return $route;
    }

    protected function getRoute($indexKey, ServerRequestInterface $request)
    {
        $validRoutes = [];

        foreach ($this->routesIndex[$indexKey] as $route) {
            $scheme = $route->getScheme();
            if (!empty($scheme) && $request->getUri()->getScheme() !== $scheme) {
                continue;
            }
            $host = $route->getHost();
            if (!empty($host) && $request->getUri()->getHost() !== $host) {
                continue;
            }
            foreach ($route->getConditions() as $condition) {
                if (!$condition->verify($request)) {
                    continue;
                }
            }
            $validRoutes[] = $route;
        }

        if (empty($validRoutes)) {
            throw new RouteConditionFailedException;
        } else if (count($validRoutes) > 1) {
            throw new ManyRoutesFoundException;
        }

        return reset($validRoutes);
    }

    protected function getCollector()
    {
        $collector = new RouteCollector(new Std, new GroupCountBased2);

        $this->routesIndex = [];
        
        foreach ($this->routes->getGroups() as $group) {
            foreach ($group->getRoutes() as $route) {
                $uri = $route->getRoute();
                $key = $route->getHttpMethod().'_'.$uri;
                if (!isset($this->routesIndex[$key])) {
                    $collector->addRoute($route->getHttpMethod(), $uri, $key);
                }
                $this->routesIndex[$key][] = $route;
            }
        }
        foreach ($this->routes->getRoutes() as $route) {
            $uri = $route->getRoute();
            $key = $route->getHttpMethod().'_'.$uri;
            if (!isset($this->routesIndex[$key])) {
                $collector->addRoute($route->getHttpMethod(), $uri, $key);
            }
            $this->routesIndex[$key][] = $route;
        }
        
        return $collector;
    }
}
