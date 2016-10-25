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
        return $this->addCondition(new Conditions\CallableCondition($callable));
    }

    public function addBodyCondition($parameter, $value = null)
    {
        return $this->addCondition(new Conditions\BodyParameterCondition($parameter, $value));
    }

    public function addQueryCondition($parameter, $value = null)
    {
        return $this->addCondition(new Conditions\QueryParameterCondition($parameter, $value));
    }

    public function addCondition(Conditions\ConditionInterface $condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }
}
