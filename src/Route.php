<?php


namespace Laasti\Directions;

use Laasti\Directions\Strategies\StrategyInterface;

/**
 * Route
 *
 */
class Route
{
    use Conditions\ConditionableTrait;
    
    protected $httpMethod;
    protected $route;
    protected $handler;
    protected $name;
    protected $group;
    protected $scheme;
    protected $host;
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
        $this->group = new RoutesGroup($strategy);
    }
    
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getRoute()
    {
        return $this->getGroup()->getPrefix().$this->route.$this->getGroup()->getSuffix();
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

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getHost()
    {
        return $this->host ? $this->host : $this->getGroup()->getHost();
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(RoutesGroup $group)
    {
        $this->group = $group;
        return $this;
    }

    public function getScheme()
    {
        return $this->scheme ? $this->scheme : $this->getGroup()->getScheme();
    }
    
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }
}
