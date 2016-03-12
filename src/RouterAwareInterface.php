<?php

namespace Laasti\Directions;

interface RouterAwareInterface
{
    public function setRouter(RouterInterface $router);

    public function getRouter();
}
