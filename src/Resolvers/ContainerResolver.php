<?php

namespace Laasti\Directions\Resolvers;

use Interop\Container\ContainerInterface;
use RuntimeException;

class ContainerResolver implements ResolverInterface
{
    const CLASS_METHOD_EXTRACTOR = "/^(.+)::(.+)$/";
    
    protected $container;
    
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function resolve($value)
    {
        $matches = [];
        if (is_string($value) && preg_match(self::CLASS_METHOD_EXTRACTOR, $value, $matches)) {
            list($matchedString, $class, $method) = $matches;
            if ($this->container instanceof ContainerInterface && $this->container->has($class)) {
                return [$this->container->get($value), $method];
            }
        } else if ($this->container instanceof ContainerInterface && $this->container->has($value)) {
            return $this->container->get($value);
        }

        if (is_callable($value)) {
            return $value;
        }
        
        throw new RuntimeException('Invalid route handler, cannot resolve: '.$value);
    }
}
