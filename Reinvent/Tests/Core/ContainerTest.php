<?php

namespace Reinvent\Tests\Core;

use PHPUnit\Framework\TestCase;
use Reinvent\Core\Support\Container;
use Reinvent\Tests\Mock\Person;
use \Exception;

class ContainerTest extends TestCase
{

    // It should allow the binding of singletons.
    public function testCanBindAndResolveSingletons()
    {
        $container = new Container();
        $container->singleton('Foo', Person::class);
        $testOne = $container->resolve('Foo');
        $testTwo = $container->resolve('Foo');
        $this->assertSame($testOne, $testTwo, "Object references should be the same.");
    }
    
    // It should allow the binding of instances.
    public function testCanBindAndResolveInstances()
    {
        $expected = new Person();
        $container = new Container();
        $container->instance('Foo', $expected);
        $actual = $container->resolve('Foo');
        $this->assertSame($expected, $actual, "Object references should be the same.");
    }
    
    // It should allow the binding of instantiables
    public function testCanBindAndResolveInstantiables()
    {
        $container = new Container();
        $container->bind('Foo', "Reinvent\Tests\Mock\Person");
        $actual = $container->resolve('Foo');
        $this->assertTrue($actual instanceof Person);
    }

    // It should allow the binding of closures.
    public function testCanBindAndResolveClosures()
    {
        $container = new Container();
        $container->bind('Foo', function () {
            return new Person();
        });

        $actual = $container->resolve('Foo');
        $this->assertTrue($actual instanceof Person);
    }

    // It should perform constructor injection
    public function testItPerformsConstructorInjection()
    {
        $container = new Container();
        $container->bind('Foo', 'Reinvent\Tests\Mock\Dependent');
        $resolved = $container->resolve('Foo');
        $injected = $resolved->getDependee();
        $this->assertTrue($injected instanceof Person);
    }
}
