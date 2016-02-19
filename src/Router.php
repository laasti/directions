<?php

namespace Laasti\Directions;

class Router implements RouterInterface
{

    /**
     *
     * @var FastRoute\Dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var RouteCollection
     */
    protected $routes;
    
    /**
     *
     * @var \Laasti\Peels\StackBuilderInterface
     */
    protected $stackBuilder;

    public function __construct(RouteCollection $routes = null, \Laasti\Peels\StackBuilderInterface $stackBuilder = null)
    {
        $this->routes = $routes;
        $this->stackBuilder = $stackBuilder;
    }
    
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getStackBuilder()
    {
        return $this->stackBuilder;
    }

        
    public function setDispatcher(FastRoute\Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    public function setStackBuilder(\Laasti\Peels\StackBuilderInterface $stackBuilder)
    {
        $this->stackBuilder = $stackBuilder;
        return $this;
    }

    /**
     * 
     * @param type $httpMethod
     * @param type $route
     * @param type $handler
     * @return RouteCollection
     */
    public function add($httpMethod, $route, $handler)
    {
        return $this->routes->addRoute($httpMethod, $route, $handler);
    }
    
    /**
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Route
     */
    public function find(\Psr\Http\Message\ServerRequestInterface $request)
    {
        return $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
    }
    
    /**
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return Response
     */
    public function findAndDispatch(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    {
        return $this->dispatch($this->find($request), $response);
    }
    
    /**
     * 
     * @param \Laasti\Directions\Route $route
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return Response
     */
    public function dispatch(Route $route, \Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    {
        $request = $request->withAttribute('_route', $route);
        foreach ($route->getMiddlewares() as $middleware) {
            $this->stackBuilder->push($middleware);
        }
        foreach ($route->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        $this->stackBuilder->push($route->getHandler());
        $runner = $this->stackBuilder->create();
        return $runner->run($request, $response);
    }

}
