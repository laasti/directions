<?php

namespace Laasti\Directions;

trait RouterAwareTrait
{
    protected $router;

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }
}
