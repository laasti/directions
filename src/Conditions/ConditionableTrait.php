<?php


namespace Laasti\Directions\Conditions;

trait ConditionableTrait
{

    protected $conditions = [];

    public function getConditions()
    {
        return $this->conditions;
    }
    
    public function addCallableCondition(callable $callable)
    {
        return $this->addCondition(new CallableCondition($callable));
    }

    public function addBodyCondition($parameter, $value = null)
    {
        return $this->addCondition(new BodyParameterCondition($parameter, $value));
    }

    public function addQueryCondition($parameter, $value = null)
    {
        return $this->addCondition(new QueryParameterCondition($parameter, $value));
    }

    public function addCondition(ConditionInterface $condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }
}
