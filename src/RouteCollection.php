<?php

namespace Laasti\Directions;

use FastRoute\DataGenerator;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;

class RouteCollection extends RouteCollector
{
    protected $routeDictionary;
    
    protected $currentRoute;
    protected $resolver;    
    
    public function __construct(ResolverInterface $resolver = null, RouteParser $routeParser = null, DataGenerator $dataGenerator = null) {
        $this->resolver = $resolver;
        $this->routeParser = $routeParser ?: new RouteParser;
        $this->dataGenerator = $dataGenerator ?: new DataGenerator;
    }
    
    public function addRoute($httpMethod, $route, $handler) 
    {
        if ($this->resolver && $this->resolver->has($handler)) {
            $handler = $this->resolver->get($handler);
        }
        return parent::addRoute($httpMethod, $route, $this->createRoute($httpMethod, $route, $handler));
    }
    
    protected function createRoute($httpMethod, $route, $handler)
    {
        $this->currentRoute = new Route($httpMethod, $route, $handler);
        return $this->currentRoute;
    }
    
    public function getResolver()
    {
        return $this->resolver;
    }
    
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }


}
