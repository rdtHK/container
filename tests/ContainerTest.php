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

class ContainerTest extends PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testAddValueThenBuilder()
    {
        $this->container->add('foo', 'foo-1');
        $this->container->add('bar', function ($container) {
            return 'bar-1';
        });
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testAddBuilderThenValue()
    {
        $this->container->add('foo', function($container) {
            return 'foo-1';
        });
        $this->container->add('bar', 'bar-1');
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testAddValueThenValue()
    {
        // Value then value
        $this->container->add('foo', 'foo-1');
        $this->container->add('bar', 'bar-1');
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testAddBuilderThenBuilder()
    {
        $this->container->add('foo', function ($container) {
            return 'foo-1';
        });
        $this->container->add('bar', function ($container) {
            return 'bar-1';
        });
        $this->assertEquals($this->container->get('foo'), 'foo-1');
        $this->assertEquals($this->container->get('bar'), 'bar-1');
    }

    public function testAddNull()
    {
        $this->container->add('foo', null);
        $this->assertNull($this->container->get('foo'));
    }

    /**
     * Ensures the container allows the inclusion
     * of raw values
     */
    public function testAddingRawValues()
    {
        $this->container->add('foo', 'bar');
        $this->assertEquals($this->container->get('foo'), 'bar');
    }

    /**
     * Ensures that addValue does not
     * call functions passed to it.
     */
    public function testAddingRawFunctions()
    {
        $this->container->addValue('foo', function() {
            return 'bar';
        });
        $foo = $this->container->get('foo');
        $this->assertTrue(is_callable($foo));
    }

    /**
     * Ensures that addBuilder only accepts callables.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage must be a callable.
     */
    public function testAddBuilder()
    {
        $this->container->addBuilder('foo', 1);
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
     * @expectedExceptionMessage Resource 'foo' is already present.
     */
    public function testAddingRepeatedItems()
    {
        $this->container->add('foo', 1);
        $this->container->add(
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
    public function testAddReturnsThis()
    {
        $y = $this->container->add('foo', 'bar');
        $this->assertSame($this->container, $y);
    }

    /**
     * Ensures the container will throw an
     * exception when the user tries to
     * define a non string key.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage  Resource names can only be strings. 'integer' provided.
     */
    public function testAddInvalidKeyType()
    {
        $this->container->add(1, 'foo');
    }
}
