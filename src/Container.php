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
     * If a callable was passed as the $resource parameter it
     * will be treated as a resource builder. Any other kinds of
     * values  will be stored directly.
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
     * Store a new resource builder in the container.
     *
     * @param string   $name     The name of the resource.
     * @param callable $resource The resource builder callback.
     *
     * @return \Rdthk\DependencyInjection\Container The container.
     */
    public function addBuilder($name, $resource)
    {
        $this->validateName($name);

        if (!is_callable($resource)) {
            $resourceType = gettype($resource);
            throw new \InvalidArgumentException(
                "'$resourceType' is not callable."
            );
        }

        $this->_builders[$name] = $resource;
        return $this;
    }

    /**
     * Store a raw value in the container.
     *
     * @param string $name     The resource name.
     * @param mixed  $resource The resource value.
     *
     * @return \Rdthk\DependencyInjection\Container The container.
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
        if (array_key_exists($name, $this->_builders)) {
            $this->_values[$name] = call_user_func(
                $this->_builders[$name], $this
            );
            unset($this->_builders[$name]);
        }

        if (!array_key_exists($name, $this->_values)) {
            throw new \InvalidArgumentException("Undeclared resource '$name'.");
        }

        return $this->_values[$name];
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
            throw new \InvalidArgumentException(
                "Resource '$name' was already declared."
            );
        }

        if (!is_string($name)) {
            $keyType = gettype($name);
            throw new \InvalidArgumentException(
                "'$keyType' is not a string."
            );
        }
    }

}
