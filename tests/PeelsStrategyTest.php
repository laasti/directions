<?php

namespace Laasti\Directions\Test;

class PeelsStrategyTest extends \PHPUnit_Framework_TestCase
{

    protected function getStrategy()
    {
        return new \Laasti\Directions\Strategies\HttpMessageStrategy(new \Zend\Diactoros\ServerRequest, new \Zend\Diactoros\Response);
    }

    public function testNoRequest()
    {
        $this->setExpectedException('RuntimeException');
        $strategy = new \Laasti\Directions\Strategies\PeelsStrategy(new \Laasti\Peels\Http\HttpRunner(new \Laasti\Peels\MiddlewareResolver));
        $strategy->setResponse(new \Zend\Diactoros\Response());
        $strategy->callRoute(new \Laasti\Directions\Route('GET', '/fake', function() {}, $strategy));
    }
    
    public function testNoResponse()
    {
        $this->setExpectedException('RuntimeException');
        $strategy = new \Laasti\Directions\Strategies\PeelsStrategy(new \Laasti\Peels\Http\HttpRunner(new \Laasti\Peels\MiddlewareResolver));
        $strategy->setRequest(new \Zend\Diactoros\ServerRequest());
        $strategy->callRoute(new \Laasti\Directions\Route('GET', '/fake', function() {}, $strategy));
    }

    public function testRouteMiddleware()
    {
        $strategy = new \Laasti\Directions\Strategies\PeelsStrategy(new \Laasti\Peels\Http\HttpRunner(new \Laasti\Peels\MiddlewareResolver));
        $strategy->setRequest(new \Zend\Diactoros\ServerRequest());
        $strategy->setResponse(new \Zend\Diactoros\Response());
        $route = new \Laasti\Directions\Route('GET', '/fake', function() {}, $strategy);
        $route->pushMiddleware(function($request, $response, $next) {return 5;});
        $this->assertTrue($strategy->callRoute($route) === 5);
    }

}
