<?php

namespace Laasti\Directions\Test;

class LeagueProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testAlias()
    {
        $container = new \League\Container\Container();
        $container->add('config', [
            'directions' => [
                'default' => []
            ]
        ]);
        $container->addServiceProvider('Laasti\Directions\Providers\LeagueDirectionsProvider');
        $this->assertInstanceOf('Laasti\Directions\Router', $container->get('directions.routers.default'));
        $this->assertInstanceOf('Laasti\Directions\Router', $container->get('Laasti\Directions\RouterInterface'));
    }
}
