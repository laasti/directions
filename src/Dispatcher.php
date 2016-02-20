<?php


namespace Laasti\Directions;

use FastRoute\Dispatcher\GroupCountBased;
use Laasti\Directions\Exceptions\MethodNotAllowedException;
use Laasti\Directions\Exceptions\RouteNotFoundException;

/**
 * Description of Dispatcher
 *
 * @author Sonia
 */
class Dispatcher extends GroupCountBased
{
    
    protected $routes;
    
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }
    
    public function dispatch($httpMethod, $uri)
    {
        list($this->staticRouteMap, $this->variableRouteData) = $this->routes->getData();
        $result = parent::dispatch($httpMethod, $uri);
        
        switch ($result[0]) {
            case self::NOT_FOUND:
                throw new RouteNotFoundException;
            case self::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException((array) $result[1]);
        }
        
        foreach ((array)$result[2] as $name => $value) {
            $result[1]->setAttribute($name, $value);
        } 
        
        return $result[1];
    }
}
