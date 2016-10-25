<?php


namespace Laasti\Directions\Conditions;

use Psr\Http\Message\ServerRequestInterface;

interface ConditionInterface
{
    public function verify(ServerRequestInterface $request);
}
