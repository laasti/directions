<?php

namespace Laasti\Directions\Test;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testRouteNotFound()
    {
        $this->setExpectedException('Laasti\Directions\Exceptions\RouteNotFoundException');

        $routes = new \Laasti\Directions\RouteCollection(new \Laasti\Directions\Strategies\HttpMessageStrategy(new \Zend\Diactoros\ServerRequest,
            new \Zend\Diactoros\Response));
        $dispatcher = new \Laasti\Directions\Locator($routes);
        $dispatcher->find('GET', '/willbenotfound', new \Zend\Diactoros\ServerRequest);
    }

    public function testMethodNotAllowed()
    {
        $this->setExpectedException('Laasti\Directions\Exceptions\MethodNotAllowedException');
        $routes = new \Laasti\Directions\RouteCollection(new \Laasti\Directions\Strategies\HttpMessageStrategy(new \Zend\Diactoros\ServerRequest,
            new \Zend\Diactoros\Response));
        $routes->addRoute('POST', '/willbenotallowed', function () {
        });
        $dispatcher = new \Laasti\Directions\Locator($routes);
        $dispatcher->find('GET', '/willbenotallowed', new \Zend\Diactoros\ServerRequest);
    }

    public function testMethodOk()
    {
        $routes = new \Laasti\Directions\RouteCollection(new \Laasti\Directions\Strategies\HttpMessageStrategy(new \Zend\Diactoros\ServerRequest,
            new \Zend\Diactoros\Response));
        $routes->addRoute(['GET'], '/willbenotallowed', function ($request, $response) {
            return $response;
        });
        $dispatcher = new \Laasti\Directions\Locator($routes);
        $route = $dispatcher->find('GET', '/willbenotallowed', new \Zend\Diactoros\ServerRequest);
        $this->assertTrue($route instanceof \Laasti\Directions\Route);
        $this->assertTrue($route->getStrategy()->callRoute($route) instanceof \Zend\Diactoros\Response);
    }
}
