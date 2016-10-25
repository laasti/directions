<?php

namespace Laasti\Directions;

use Laasti\Directions\Strategies\StrategyInterface;

class RouteCollection
{
    /**
     *
     * @var Route
     */
    protected $currentRoute;
    /**
     *
     * @var StrategyInterface
     */
    protected $defaultStrategy;

    protected $namedRoutes = [];
    protected $routes = [];
    protected $groups = [];

    public function __construct(StrategyInterface $defaultStrategy)
    {
        $this->defaultStrategy = $defaultStrategy;
    }

    /**
     *
     * @param string|array $httpMethod
     * @param string $pathinfo
     * @param callable|mixed $handler
     * @return Route
     */
    public function addRoute($httpMethod, $pathinfo, $handler, $middlewares = [])
    {
        $route = $this->createRoute($httpMethod, $pathinfo, $handler, $middlewares);
        $this->routes[] = $route;
        return $route;
    }

    /**
     *
     * @param string|array $httpMethod
     * @param string $route
     * @param callable|string $handler
     * @param array $middlewares
     * @return Route
     */
    protected function createRoute($httpMethod, $route, $handler, $middlewares = [])
    {
        $this->saveCurrentRoute();
        $this->currentRoute = new Route($httpMethod, $route, $handler, $this->defaultStrategy);
        $this->currentRoute->setMiddlewares($middlewares);
        return $this->currentRoute;
    }

    protected function saveCurrentRoute()
    {
        if ($this->currentRoute instanceof Route) {
            $name = $this->currentRoute->getName();
            if (!is_null($name)) {
                $this->namedRoutes[$name] = $this->currentRoute;
            }
        }
    }

    /**
     * Return current default strategy
     * @return StrategyInterface
     */
    public function getDefaultStrategy()
    {
        return $this->defaultStrategy;
    }

    /**
     * Set new default strategy
     * @return StrategyInterface
     */
    public function setDefaultStrategy(StrategyInterface $defaultStrategy)
    {
        $this->defaultStrategy = $defaultStrategy;
        return $this;
    }

    /**
     *
     * @param string $name
     * @return Route
     * @throws \OutOfBoundsException
     */
    public function getRouteByName($name)
    {
        $this->saveCurrentRoute();
        
        if (isset($this->namedRoutes[$name])) {
            return $this->namedRoutes[$name];
        }

        foreach ($this->groups as $group) {
            foreach ($group->getRoutes() as $route) {
                if ($route->getName() === $name) {
                    return $route;
                }
            }
        }

        throw new \OutOfBoundsException('No registered route with the name: '.$name);
    }

    public function addGroup($prefix = null, $suffix = null, $domain = null, $scheme = null)
    {
        $group = new RoutesGroup($this->defaultStrategy, $prefix, $suffix, $domain, $scheme);
        $this->groups[] = $group;
        
        return $group;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getGroups()
    {
        return $this->groups;
    }
}
