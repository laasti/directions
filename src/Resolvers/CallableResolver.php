<?php

namespace Laasti\Directions\Resolvers;

use RuntimeException;

class CallableResolver implements ResolverInterface
{
    const CLASS_METHOD_EXTRACTOR = "/^(.+)::(.+)$/";
    
    public function resolve($value)
    {
        if (is_callable($value)) {
            return $value;
        }
        
        throw new RuntimeException('Invalid route handler, cannot resolve: '.$value);
    }
}
