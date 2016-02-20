<?php

namespace Laasti\Directions\Strategies;

use Laasti\Directions\Route;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpMessageStrategy implements StrategyInterface
{
    /**
     *
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * 
     * @var ResponseInterface 
     */
    protected $response;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }


    public function callRoute(Route $route)
    {
        $request = $this->getRequest()->withAttribute('_route', $route);
        foreach ($route->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        return call_user_func_array($route->getHandler(), [$request, $this->getResponse()]);
    }
}
