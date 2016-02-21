<?php


namespace Laasti\Directions;

use Laasti\Directions\Strategies\StrategyInterface;

/**
 * Description of Route
 *
 * @author Sonia
 */
class Route
{
    protected $httpMethod;
    protected $route;
    protected $handler;
    protected $middlewares = [];
    protected $attributes = [];
    
    /**
     *
     * @var StrategyInterface
     */
    protected $strategy;
    
    public function __construct($httpMethod, $route, $handler, StrategyInterface $strategy)
    {
        $this->httpMethod = $httpMethod;
        $this->route = $route;
        $this->handler = $handler;
        $this->strategy = $strategy;
    }
    
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getHandler()
    {
        return $this->handler;
    }
    
    public function getMiddlewares()
    {
        return $this->middlewares;
    }
    
    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $middlewares;
        
        return $this;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    public function callStrategy()
    {
        return $this->strategy->callRoute($this);
    }

    public function setStrategy(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }
    
    public function pushMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
        
        return $this;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getAttribute($attribute, $default = null)
    {
        return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : $default;
    }
    
    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
        return $this;
    }
    
    public function removeAttribute($attribute)
    {
        unset($this->attributes[$attribute]);
    }
    
    public function hasAttribute($attribute)
    {
        return array_key_exists($attribute, $this->attributes);
    }
}
