<?php


namespace Laasti\Directions;

/**
 * Description of Dispatcher
 *
 * @author Sonia
 */
class Dispatcher extends \FastRoute\Dispatcher\GroupCountBased
{
    
    protected $routes;
    
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }
    
    public function dispatch($httpMethod, $uri)
    {
        list($this->staticRouteMap, $this->variableRouteData) = $this->routes->getData();
        list($result, $routeOrMethods, $data) = parent::dispatch($httpMethod, $uri);
        
        switch ($result) {
            case \FastRoute::NOT_FOUND:
                throw new RouteNotFoundException;
            case \FastRoute::MEtho:
                throw new MethodNotAllowedException((array) $routeOrMethods);
        }
        
        foreach ((array)$data as $name => $value) {
            $routeOrMethods->setAttribute($name, $value);
        } 
        
        return $routeOrMethods;
    }
}
