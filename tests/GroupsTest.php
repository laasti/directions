<?php

namespace Laasti\Directions\Test;

use Laasti\Directions\RouteCollection;
use Laasti\Directions\Router;
use Laasti\Directions\Strategies\HttpMessageStrategy;
use PHPUnit_Framework_TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequestFactory;

class GroupsTest extends PHPUnit_Framework_TestCase
{

    protected function getRouter()
    {
        return new Router(new RouteCollection(new HttpMessageStrategy));
    }

    protected function fakeServerParams($uri, $query = 'p=1', $scheme = 'http', $host = 'localhost')
    {
        return [
            "REDIRECT_STATUS" => "200",
            "HTTP_HOST" => $host,
            "SERVER_NAME" => $host,
            "SERVER_ADDR" => "::1",
            "SERVER_PORT" => "80",
            "REMOTE_ADDR" => "::1",
            "DOCUMENT_ROOT" => "C:/wamp/www/site",
            "REQUEST_SCHEME" => $scheme,
            "HTTPS" => $scheme === 'https',
            "SCRIPT_FILENAME" =>"C:/wamp/www/site/index.php",
            "REMOTE_PORT" => "59428",
            "REDIRECT_URL" => "/site".$uri,
            "REDIRECT_QUERY_STRING" => $uri,
            "GATEWAY_INTERFACE" => "CGI/1.1",
            "SERVER_PROTOCOL" => "HTTP/1.1",
            "REQUEST_METHOD" => "GET",
            "QUERY_STRING" => $uri."&".$query,
            "REQUEST_URI" => "/site".$uri."?".$query,
            "SCRIPT_NAME" => "/site/index.php",
            "PHP_SELF" => "/site/index.php",
        ];
    }

    public function testPrefixGroup()
    {
        $router = $this->getRouter();
        $group = $router->createGroup('/prefix');
        $route = $group->add('GET', '/test', function() {return new TextResponse('TEST');});
        $this->assertEquals('/prefix/test', $route->getRoute());
        $this->assertInstanceOf('Zend\Diactoros\Response', $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/prefix/test')), new Response()));
    }

    public function testSuffixGroup()
    {
        $router = $this->getRouter();
        $group = $router->createGroup('', '.json');
        $route = $group->add('GET', '/test', function() {return new TextResponse('TEST');});
        $this->assertEquals('/test.json', $route->getRoute());
        $response = $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/test.json')), new Response());
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);
        $this->assertEquals('TEST', (string) $response->getBody());
    }

    public function testSchemeGroup()
    {
        $router = $this->getRouter();
        $route = $router->add('GET', '/test', function() {return new TextResponse('TEST');});
        $route->setScheme('http');

        $group = $router->createGroup('', '', null, 'https');
        $route = $group->add('GET', '/test', function() {return new TextResponse('SECURE TEST');});

        $response = $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/test')), new Response());
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);
        $this->assertEquals('TEST', (string) $response->getBody());

        $response = $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/test', 'p=2', 'https')), new Response());
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);
        $this->assertEquals('SECURE TEST', (string) $response->getBody());
    }

    public function testHostGroup()
    {
        $router = $this->getRouter();
        $route = $router->add('GET', '/test', function() {return new TextResponse('TEST');});
        $route->setHost('localhost');

        $group = $router->createGroup('', '', 'example.com', null);
        $route = $group->add('GET', '/test', function() {return new TextResponse('HOST TEST');});

        $response = $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/test')), new Response());
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);
        $this->assertEquals('TEST', (string) $response->getBody());

        $response = $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/test', 'p=2', 'http', 'example.com')), new Response());
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);
        $this->assertEquals('HOST TEST', (string) $response->getBody());
    }

    public function testConditionGroup()
    {
        $router = $this->getRouter();
        $route = $router->add('GET', '/test', function() {return new TextResponse('TEST');});
        $route->addQueryCondition('page');

        $group = $router->createGroup();
        $group->addQueryCondition('test');
        $route = $group->add('GET', '/test', function() {return new TextResponse('CONDITION TEST');});

        $response = $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/test', 'page=2'), ['page' => 2]), new Response());
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);
        $this->assertEquals('TEST', (string) $response->getBody());

        $response = $router->dispatch(ServerRequestFactory::fromGlobals($this->fakeServerParams('/test', 'test=2'), ['test' => 2]), new Response());
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);
        $this->assertEquals('CONDITION TEST', (string) $response->getBody());
    }

}
