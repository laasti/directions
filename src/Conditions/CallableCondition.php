<?php

namespace Laasti\Directions\Conditions;

class CallableCondition implements ConditionInterface
{

    protected $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function verify(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $callable = $this->callable;
        return $callable($request);
    }
}
