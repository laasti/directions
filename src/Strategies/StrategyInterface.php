<?php


namespace Laasti\Directions\Strategies;

use Laasti\Directions\Route;

interface StrategyInterface
{
    public function callRoute(Route $route);
}
