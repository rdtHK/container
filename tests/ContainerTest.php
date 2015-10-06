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
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * Ensures the container will
     * call builder functions.
     */
    public function testAddingBuilderFunctions()
    {
        $container = new Container();
        $container->add(
            'foo',
            function ($container) {
                return 'bar';
            }
        );

        $this->assertEquals($container->get('foo'), 'bar');
    }

    /**
     * Ensures the container allows the inclusion
     * of raw values
     */
    public function testAddingDirectValues()
    {
        $container = new Container();
        $container->add('foo', 'bar');

        $this->assertEquals($container->get('foo'), 'bar');
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
        $container = new Container();
        $container->get('foo');
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
        $container = new Container();
        $container->add('foo', 1);
        $container->add(
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
        $container = new Container();
        $y = $container->add('foo', 'bar');

        $this->assertSame($container, $y);
    }

    /**
     * Ensures the container will throw an
     * exception when the user tries to
     * define a non string key.
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage  Resource names can only be strings. 'integer' provided.
     */
    public function testAddingInvalidKeyType()
    {
        $container = new Container();
        $container->add(1, 'foo');
    }
}
