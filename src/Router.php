<?php

namespace Laasti\Directions;

use Laasti\Directions\Locator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{

    /**
     *
     * @var Locator
     */
    protected $locator;

    /**
     *
     * @var RouteCollection
     */
    protected $routes;


    public function __construct(RouteCollection $routes = null, Locator $locator = null)
    {
        $this->routes = $routes;
        $this->locator = $locator ?: new Locator($routes);
    }
    
    public function getLocator()
    {
        return $this->locator;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
  
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
        return $this;
    }

    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
        $this->locator = new Locator($routes);
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
     * @param ServerRequestInterface
     * @param ResponseInterface
     * @return ServerRequestInterface
     */
    public function find(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $request->withAttribute('pathinfo', $this->getPathInfo($request))
                ->withAttribute('basepath', $this->getBasePath($request))
                ->withAttribute('route', $this->locator->find($request->getMethod(), $this->getPathInfo($request)));
    }

    /**
     *
     * @param string $method Method
     * @param string $route Route
     * @return Route
     */
    public function findRoute($method, $route)
    {
        return $this->locator->find($method, $route);
    }
    
    /**
     *
     * @param string HTTP Method
     * @param string
     * @return mixed
     */
    public function findAndDispatch(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->dispatch($request->find($request, $response), $response);
    }
    
    /**
     * 
     * @param Route $route
     * @return mixed
     */
    public function dispatch(ServerRequestInterface $request = null, ResponseInterface $response = null)
    {
        if (!isset($request->getAttributes()['route'])) {
            $request = $this->find($request, $response);
        }
        $route = $request->getAttribute('route');

        if ($route->getStrategy() instanceof Strategies\HttpAwareStrategyInterface) {
            $route->getStrategy()->setRequest($request);
            $route->getStrategy()->setResponse($response);
        }
        
        return $route->callStrategy();
    }

    /**
     *
     * @param \Laasti\Directions\Route $route
     */
    public function dispatchRoute(Route $route)
    {
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
