<?php


namespace Laasti\Directions\Strategies;

use Laasti\Directions\Route;
use Laasti\Peels\StackBuilderInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class PeelsStrategy implements StrategyInterface, HttpAwareStrategyInterface
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
    protected $runner;

    public function __construct(\Laasti\Peels\Http\HttpRunner $runner, RequestInterface $request = null, ResponseInterface $response = null)
    {
        $this->runner = $runner;
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

    public function getRunner()
    {
        return $this->runner;
    }

    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    public function callRoute(Route $route)
    {
        if (is_null($this->getRequest()) || is_null($this->getResponse())) {
            throw new RuntimeException('You need to set the request and the response before calling callRoute.');
        }
        $request = $this->getRequest()->withAttribute('_route', $route);
        $runner = clone $this->getRunner();
        foreach ($route->getMiddlewares() as $middleware) {
            $runner->push($middleware);
        }
        foreach ($route->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        $runner->push($route->getHandler());

        return $runner($request, $this->getResponse());
    }
}
