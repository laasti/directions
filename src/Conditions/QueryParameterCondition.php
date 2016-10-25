<?php

namespace Laasti\Directions\Conditions;

class QueryParameterCondition implements ConditionInterface
{

    protected $parameter;
    protected $value;

    public function __construct($parameter, $value = null)
    {
        $this->parameter = $parameter;
        $this->value = $value;
    }

    public function verify(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $parameters = $request->getQueryParams();
        
        if (is_null($this->value)) {
            return isset($parameters[$this->parameter]);
        } else if (!isset($parameters[$this->parameter])) {
            return false;
        }
        return $parameters[$this->parameter] === $this->value;
    }
}
