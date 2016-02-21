<?php

namespace Laasti\Directions;

use Laasti\Directions\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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


    public function __construct(RouteCollection $routes = null, Dispatcher $dispatcher = null)
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
    public function find(ServerRequestInterface $request, ResponseInterface $response)
    {
        $request = $request->withAttribute('pathinfo', $this->getPathInfo($request))
                ->withAttribute('basepath', $this->getBasePath($request));
        return $this->dispatcher->dispatch($request->getMethod(), $this->getPathInfo($request));
    }
    
    /**
     *
     * @param string HTTP Method
     * @param string
     * @return mixed
     */
    public function findAndDispatch(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->dispatch($this->find($request, $response), $request, $response);
    }
    
    /**
     * 
     * @param Route $route
     * @return mixed
     */
    public function dispatch(Route $route, ServerRequestInterface $request = null, ResponseInterface $response = null)
    {
        if ($route->getStrategy() instanceof Strategies\HttpAwareStrategyInterface) {
            $route->getStrategy()->setRequest($request);
            $route->getStrategy()->setResponse($response);
        }
        return $route->callStrategy();
    }

    protected function getPathInfo(ServerRequestInterface $request)
    {
        return str_replace($this->getBasePath($request), '', $request->getUri()->getPath());
    }

    protected function getBasePath(ServerRequestInterface $request)
    {
        $server = $request->getServerParams();
        $folder = '';
        if (isset($server['SCRIPT_NAME'])) {
            $folder = pathinfo($server['SCRIPT_NAME'], PATHINFO_DIRNAME);
        } else if (isset($server['PHP_SELF'])) {
            $folder = pathinfo($server['PHP_SELF'], PATHINFO_DIRNAME);
        }
        return $folder;
    }

}
