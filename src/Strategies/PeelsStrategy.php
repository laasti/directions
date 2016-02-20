<?php


namespace Laasti\Directions\Strategies;

use Laasti\Directions\Route;
use Laasti\Peels\StackBuilderInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class PeelsStrategy implements StrategyInterface
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
    /**
     *
     * @var StackBuilderInterface
     */
    protected $stack;

    public function __construct(StackBuilderInterface $stack, RequestInterface $request = null, ResponseInterface $response = null)
    {
        $this->stack = $stack;
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

    public function getStack()
    {
        return $this->stack;
    }

    public function setStack(StackBuilderInterface $stack)
    {
        $this->stack = $stack;
        return $this;
    }

    public function callRoute(Route $route)
    {
        if (is_null($this->getRequest()) || is_null($this->getResponse())) {
            throw new RuntimeException('You need to set the request and the response before calling callRoute.');
        }
        $request = $this->getRequest()->withAttribute('_route', $route);
        foreach ($route->getMiddlewares() as $middleware) {
            $this->stack->push($middleware);
        }
        foreach ($route->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        $this->stack->push($route->getHandler());
        $runner = $this->stack->create();
        return $runner($request, $this->getResponse());
    }
}
