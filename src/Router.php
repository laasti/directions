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
     * @param array $middlewares
     * @return RouteCollection
     */
    public function add($httpMethod, $route, $handler, $middlewares = [])
    {
        return $this->routes->addRoute($httpMethod, $route, $handler, $middlewares);
    }

    public function createGroup($prefix = null, $suffix = null, $host = null, $scheme = null)
    {
        return $this->routes->addGroup($prefix, $suffix, $host, $scheme);
    }

    /**
     *
     * @param ServerRequestInterface
     * @param ResponseInterface
     * @return ServerRequestInterface
     */
    public function find(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $route = $this->locator->find($request->getMethod(), $this->getPathInfo($request), $request);

        $request = $request->withAttribute('pathinfo', $this->getPathInfo($request))
                    ->withAttribute('basepath', $this->getBasePath($request))
                    ->withAttribute('route', $route);

        foreach ($route->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        if (is_callable($next)) {
            return $next($request, $response);
        } else {
            return $request;
        }
    }

    /**
     *
     * @param string HTTP Method
     * @param string
     * @return mixed
     */
    public function findAndDispatch(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->dispatch($this->find($request, $response), $response);
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
		$base = $this->getBasePath($request);
        return '/'.ltrim(preg_replace('/^'.preg_quote($base, '/').'/', '', $request->getUri()->getPath()), '/');
    }

    protected function getBasePath(ServerRequestInterface $request)
    {
        $urlBuilder = new UrlBuilder($request);
        return $urlBuilder->getBaseUri();
    }

}
