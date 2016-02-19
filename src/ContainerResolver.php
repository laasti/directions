<?php

namespace Laasti\Directions;

class ContainerResolver implements ResolverInterface
{
    protected $container;
    
    public function __construct(\Interop\Container\ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function resolve($value)
    {
        if ($this->container->has($value)) {
            return $this->container->get($value);
        } else if (is_callable($value)) {
            return $value;
        }
        
        throw new \RuntimeException('Invalid route handler, cannot resolve: '.$value);
    }
}
