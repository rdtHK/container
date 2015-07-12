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

namespace Rdthk\DependencyInjection;

/**
 * Small dependency injection container that can also double as
 * a service locator. Or maybe the opposite.
 */
class Container
{

    private $_builders = [];
    private $_values = [];

    /**
     * Stores a new resource in the container.
     *
     * If a callable was passed as the $resource parameter,
     * when this element is first retrieved, it will be
     * stored as a builder and called and its return value cached when
     * the resource is first retrieved.
     *
     * @param string $name     Name associated with the resource.
     * @param mixed  $resource Callback or resource to be stored.
     *
     * @return \Rdthk\DependencyInjection\Container The container.
     */
    public function add($name, $resource)
    {
        if (is_callable($resource)) {
            $this->addBuilder($name, $resource);
        } else {
            $this->addValue($name, $resource);
        }

        return $this;
    }

    /**
     * Adds a new resource builder to the container.
     *
     * @param string   $name     The name of the resource.
     * @param callable $resource The resource builder callback.
     */
    public function addBuilder($name, $resource)
    {
        $this->validateName($name);

        if (!is_callable($resource)) {
            $resourceType = gettype($resource);
            throw new \InvalidArgumentException(
            "The resource parameter for addBuilder must be a callable. "
            . "'$resourceType' provided."
            );
        }

        $this->_builders[$name] = $resource;
        return $this;
    }

    /**
     * Adds a new raw value to the container.
     *
     * @param string $name     The resource name.
     * @param mixed  $resource The resource value.
     */
    public function addValue($name, $resource)
    {
        $this->validateName($name);
        $this->_values[$name] = $resource;
        return $this;
    }

    /**
     * Returns the resource associated with the supplied name.
     *
     * @param string $name Name associated with the resource.
     *
     * @return mixed
     */
    public function get($name)
    {
        if (!empty($this->_builders[$name])) {
            $this->_values[$name] = call_user_func($this->_builders[$name], $this);
            unset($this->_builders[$name]);
        }

        if (empty($this->_values[$name])) {
            throw new \InvalidArgumentException("Undeclared resource '$name'.");
        }

        return $this->_values[$name];
    }

    /**
     * Constructs a builder for the supplied class.
     * The method receives a class name and an array in the form
     * ['methodName' => ['list', 'of', 'dependencies']]
     *
     * generates a builder function and adds it to the container.
     *
     * @param string $class Name associated with the resource.
     * @param array  $definition List of methods to be calld and its parameters.
     */
    public function addClass($class, $definition)
    {
        $builder = function ($container) use ($class, $definition) {
            $reflectionClass = new \ReflectionClass($class);
            $args = [];

            if (isset($definition['__construct'])) {
                $args = Container::buildDependencies(
                                $container, $definition['__construct']
                );
            }

            $object = $reflectionClass->newInstanceArgs($args);

            foreach ($definition as $method => $dependencies) {
                if ($method === '__construct') {
                    continue;
                }

                $args = Container::buildDependencies($container, $dependencies);
                $reflectionMethod = $reflectionClass->getMethod($method);
                $reflectionMethod->invokeArgs($object, $args);
            }

            return $object;
        };

        $this->addBuilder($class, $builder);

        return $this;
    }

    /**
     * Ensures that the name is a string and that it is not present
     * in the container.
     *
     * @param  string $name The name of the resource.
     */
    private function validateName($name)
    {
        if (isset($this->_builders[$name]) || isset($this->_values[$name])) {
            throw new \InvalidArgumentException("Resource '$name' is already present.");
        }

        if (!is_string($name)) {
            $keyType = gettype($name);
            throw new \InvalidArgumentException(
            "Resource names can only be strings. '$keyType' provided."
            );
        }
    }

    /**
     * Retrieve all dependencies from a list.
     * TODO: Change it to a regular public method called getAll($resources).
     *
     * @param \Rdthk\DependencyInjection\Container $container
     * @param string[] $dependencies A list of dependency names to be retrieved.
     *
     * @return array
     */
    private static function buildDependencies($container, $dependencies)
    {
        $args = [];

        foreach ($dependencies as $dep) {
            $args[] = $container->get($dep);
        }

        return $args;
    }

}
