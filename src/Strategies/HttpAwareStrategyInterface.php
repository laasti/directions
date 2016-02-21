<?php

namespace Laasti\Directions\Strategies;

interface HttpAwareStrategyInterface
{

    public function setRequest(\Psr\Http\Message\RequestInterface $request);
    public function setResponse(\Psr\Http\Message\ResponseInterface $response);

}
