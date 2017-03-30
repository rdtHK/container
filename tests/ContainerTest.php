<?php

/**
 * Copyright 2015 Mário Camargo Palmeira
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
use Rdthk\DependencyInjection\Container;
use Rdthk\DependencyInjection\SingletonScope;
use Rdthk\DependencyInjection\DependentScope;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testBindValueThenFactory()
    {
        $this->container->bind('foo', 'foo-1');
        $this->container->bind('bar', function ($container) {
            return 'bar-1';
        });
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testBindFactoryThenValue()
    {
        $this->container->bind('foo', function($container) {
            return 'foo-1';
        });
        $this->container->bind('bar', 'bar-1');
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testBindValueThenValue()
    {
        // Value then value
        $this->container->bind('foo', 'foo-1');
        $this->container->bind('bar', 'bar-1');
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testBindFactoryThenFactory()
    {
        $this->container->bind('foo', function ($container) {
            return 'foo-1';
        });
        $this->container->bind('bar', function ($container) {
            return 'bar-1';
        });
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testBindNull()
    {
        $this->container->bind('foo', null);
        $this->assertNull($this->container->get('foo'));
    }

    public function testDefaultScopeIsDependent()
    {
        $this->container->bind('foo', function ($container) {
            return new ContainerTest;
        });

        $this->assertNotSame(
            $this->container->get('foo'),
            $this->container->get('foo')
        );
    }

    public function testDependentScope()
    {
        $this->container->bind('foo', function ($container) {
            return new ContainerTest;
        }, DependentScope::class);

        $this->assertNotSame(
            $this->container->get('foo'),
            $this->container->get('foo')
        );
    }

    public function testSingletonScope()
    {
        $this->container->bind('foo', function ($container) {
            return new ContainerTest;
        }, SingletonScope::class);

        $this->assertSame(
            $this->container->get('foo'),
            $this->container->get('foo')
        );
    }

    /**
     * Ensures the container allows the inclusion
     * of raw values
     */
    public function testBindingRawValues()
    {
        $this->container->bind('foo', 'bar');
        $this->assertEquals($this->container->get('foo'), 'bar');
    }

    /**
     * Ensures that bindValue does not
     * call functions passed to it.
     */
    public function testBindingRawFunctions()
    {
        $this->container->bindValue('foo', function() {
            return 'bar';
        });
        $foo = $this->container->get('foo');
        $this->assertTrue(is_callable($foo));
    }

    /**
     * Ensures that bindFactory only accepts callables.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage 'integer' is not callable.
     */
    public function testBindFactory()
    {
        $this->container->bindFactory('foo', 1);
    }

    /**
     * Ensures the container will throw an
     * exception when asked for an undefined
     * resource.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Undeclared resource 'foo'.
     */
    public function testAskingForUndefinedItem()
    {
        $this->container->get('foo');
    }

    /**
     * Ensures the container will throw an
     * exception when the user tries to insert
     * two values with the same name.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Resource 'foo' was already declared.
     */
    public function testAddingRepeatedItems()
    {
        $this->container->bind('foo', 1);
        $this->container->bind(
            'foo',
            function ($container) {
                return 'bar';
            }
        );
    }

    /**
     * Ensures that Container::add returns
     * $this.
     */
    public function testBindReturnsThis()
    {
        $y = $this->container->bind('foo', 'bar');
        $this->assertSame($this->container, $y);
    }

    /**
     * Ensures the container will throw an
     * exception when the user tries to
     * define a non string key.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage  'integer' is not a string.
     */
    public function testBindInvalidKeyType()
    {
        $this->container->bind(1, 'foo');
    }
}
