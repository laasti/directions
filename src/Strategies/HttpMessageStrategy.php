<?php

namespace Laasti\Directions\Strategies;

use Laasti\Directions\Route;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class HttpMessageStrategy implements StrategyInterface, HttpAwareStrategyInterface
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

    public function __construct(RequestInterface $request = null, ResponseInterface $response = null)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function callRoute(Route $route)
    {
        if (is_null($this->getRequest()) || is_null($this->getResponse())) {
            throw new RuntimeException('You need to set the request and the response before calling callRoute.');
        }
        $request = $this->getRequest()->withAttribute('_route', $route);
        foreach ($route->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        return call_user_func_array($route->getHandler(), [$request, $this->getResponse()]);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }
}
