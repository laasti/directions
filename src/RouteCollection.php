<?php

namespace Laasti\Directions;

use FastRoute\DataGenerator;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use Laasti\Directions\Resolvers\ResolverInterface;
use Laasti\Directions\Strategies\StrategyInterface;

class RouteCollection extends RouteCollector
{
    protected $routeDictionary;
    protected $currentRoute;
    /**
     *
     * @var StrategyInterface
     */
    protected $defaultStrategy;

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

    protected function createRoute($httpMethod, $route, $handler, $middlewares = [])
    {
        $this->currentRoute = new Route($httpMethod, $route, $handler, $this->defaultStrategy);
        $this->currentRoute->setMiddlewares($middlewares);
        return $this->currentRoute;
    }

    public function getDefaultStrategy()
    {
        return $this->defaultStrategy;
    }


    public function setDefaultStrategy(StrategyInterface $defaultStrategy)
    {
        $this->defaultStrategy = $defaultStrategy;
        return $this;
    }

}
