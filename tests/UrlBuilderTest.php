<?php

namespace Laasti\Directions\Test;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
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
            "QUERY_STRING" => "/site/2",
            "REQUEST_URI" => "/site/2",
            "SCRIPT_NAME" => "/site/index.php",
            "PHP_SELF" => "/site/index.php",
        ];
    }

    public function testCreate()
    {

        $builder = new \Laasti\Directions\UrlBuilder(\Zend\Diactoros\ServerRequestFactory::fromGlobals($this->fakeServerParams()));

        $this->assertEquals('/site/test', $builder->create('/test'));
        $this->assertEquals('/test', $builder->create('/test', [], true));
        $this->assertEquals('/test/10', $builder->create('/test/{id}', ['id' => 10]));
    }

    public function testNamedRoutes()
    {

    }

}
