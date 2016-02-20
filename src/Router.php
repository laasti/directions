<?php

namespace Laasti\Directions;

use FastRoute\Dispatcher;

class Router implements RouterInterface
{

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var RouteCollection
     */
    protected $routes;


    public function __construct(RouteCollection $routes = null, \Laasti\Directions\Dispatcher $dispatcher = null)
    {
        $this->routes = $routes;
        $this->dispatcher = $dispatcher ?: new Dispatcher($routes);
    }
    
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
  
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
        $this->dispatcher = new Dispatcher($routes);
        return $this;
    }

    /**
     * 
     * @param string|array $httpMethod
     * @param string $route
     * @param mixed $handler
     * @return RouteCollection
     */
    public function add($httpMethod, $route, $handler)
    {
        return $this->routes->addRoute($httpMethod, $route, $handler);
    }
    
    /**
     * 
     * @param string HTTP Method
     * @param string
     * @return Route
     */
    public function find($httpMethod, $route)
    {
        return $this->dispatcher->dispatch($httpMethod, $route);
    }
    
    /**
     *
     * @param string HTTP Method
     * @param string
     * @return mixed
     */
    public function findAndDispatch($httpMethod, $route)
    {
        return $this->dispatch($this->find($httpMethod, $route));
    }
    
    /**
     * 
     * @param Route $route
     * @return mixed
     */
    public function dispatch(Route $route)
    {
        return $route->callStrategy();
    }

}
