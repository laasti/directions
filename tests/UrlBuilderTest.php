<?php

namespace Laasti\Directions\Test;

use Laasti\Directions\RouteCollection;
use Laasti\Directions\Strategies\HttpMessageStrategy;
use Laasti\Directions\UrlBuilder;
use PHPUnit_Framework_TestCase;
use Zend\Diactoros\ServerRequestFactory;

class UrlBuilderTest extends PHPUnit_Framework_TestCase
{

    protected function fakeServerParams()
    {
        return [
            "REDIRECT_STATUS" => "200",
            "HTTP_HOST" => "localhost",
            "SERVER_NAME" => "localhost",
            "SERVER_ADDR" => "::1",
            "SERVER_PORT" => "80",
            "REMOTE_ADDR" => "::1",
            "DOCUMENT_ROOT" => "C:/wamp/www/site",
            "REQUEST_SCHEME" => "http",
            "SCRIPT_FILENAME" =>"C:/wamp/www/site/index.php",
            "REMOTE_PORT" => "59428",
            "REDIRECT_URL" => "/site/2",
            "REDIRECT_QUERY_STRING" => "/site/2",
            "GATEWAY_INTERFACE" => "CGI/1.1",
            "SERVER_PROTOCOL" => "HTTP/1.1",
            "REQUEST_METHOD" => "GET",
            "QUERY_STRING" => "/2&p=2",
            "REQUEST_URI" => "/site/2?p=2",
            "SCRIPT_NAME" => "/site/index.php",
            "PHP_SELF" => "/site/index.php",
        ];
    }

    public function testCreate()
    {

        $builder = new UrlBuilder(ServerRequestFactory::fromGlobals($this->fakeServerParams()));

        $this->assertEquals('/site/test', $builder->create('/test'));
        $this->assertEquals('http://localhost/site/test', $builder->create('/test', [], true));
        $this->assertEquals('/site/2', $builder->getCurrentUri());
        $this->assertEquals('http://localhost/site/2', $builder->getCurrentUri(true));
        //$this->assertEquals('http://localhost/site/2?p=2', $builder->getCurrentUri(true, true));
        $this->assertEquals('/site/test/10', $builder->create('/test/{id}', ['id' => 10]));
    }

    public function testNamedRoutes()
    {
        $routes = new RouteCollection(new HttpMessageStrategy);
        $routes->addRoute('GET', '/user/{id}', function() {})->setName('UserProfile');
        $builder = new UrlBuilder(ServerRequestFactory::fromGlobals($this->fakeServerParams()), $routes);

        $this->assertEquals('/site/user/23', $builder->createByName('UserProfile', ['id' => 23]));

    }

}
