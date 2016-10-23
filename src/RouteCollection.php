<?php

namespace Laasti\Directions;

use FastRoute\DataGenerator;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use Laasti\Directions\Strategies\StrategyInterface;

class RouteCollection extends RouteCollector
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

    public function __construct(StrategyInterface $defaultStrategy,  RouteParser $routeParser = null, DataGenerator $dataGenerator = null)
    {
        $this->defaultStrategy = $defaultStrategy;
        $this->routeParser = $routeParser ? : new RouteParser\Std;
        $this->dataGenerator = $dataGenerator ? : new DataGenerator\GroupCountBased;
        parent::__construct($this->routeParser, $this->dataGenerator);
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
        parent::addRoute($httpMethod, $pathinfo, $route);

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
        if ($this->currentRoute instanceof Route) {
            $name = $this->currentRoute->getName();
            if (!is_null($name)) {
                $this->namedRoutes[] = $this->currentRoute;
            }
        }
        $this->currentRoute = new Route($httpMethod, $route, $handler, $this->defaultStrategy);
        $this->currentRoute->setMiddlewares($middlewares);
        return $this->currentRoute;
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
        if (isset($this->namedRoutes[$name])) {
            return $this->namedRoutes[$name];
        }

        throw new \OutOfBoundsException('No registered route with the name: '.$name);
    }

}
