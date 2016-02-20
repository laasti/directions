<?php

namespace Laasti\Directions\Test;

class ContainerResolverTest extends \PHPUnit_Framework_TestCase
{
    public static function staticMethod()
    {

    }
    public function publicMethod()
    {

    }

    public function testCallablesOnly()
    {
        $resolver = new \Laasti\Directions\Resolvers\ContainerResolver;
        $this->assertTrue(is_callable($resolver->resolve(function() {})));
        $this->assertTrue(is_callable('str_replace'));
        $this->assertTrue(is_callable('Laasti\Directions\Test\ContainerResolverTest::staticMethod'));
        $this->assertTrue(is_callable([$this, 'publicMethod']));
    }

    public function testContainerCall()
    {
        $container = $this->getMockBuilder('Interop\Container\ContainerInterface')->setMethods(['has', 'get'])->getMock();
        $container->expects($this->once())->method('has')->will($this->returnValue(true));
        $container->expects($this->once())->method('get')->will($this->returnValue(function() {}));

        $resolver = new \Laasti\Directions\Resolvers\ContainerResolver($container);
        $this->assertTrue(is_callable($resolver->resolve('anykeywillresolve')));
    }

    public function testContainerCallWithMethod()
    {
        $container = $this->getMockBuilder('Interop\Container\ContainerInterface')->setMethods(['has', 'get'])->getMock();
        $container->expects($this->once())->method('has')->will($this->returnValue(true));
        $container->expects($this->once())->method('get')->will($this->returnValue($this));

        $resolver = new \Laasti\Directions\Resolvers\ContainerResolver($container);
        $this->assertTrue(is_callable($resolver->resolve('anykeywillresolve::publicMethod')));
    }
    

}
