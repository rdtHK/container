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
     * called with the container as a parameter and its
     * return value will be cached.
     *
     * @param string $name     Name associated with the resource.
     * @param mixed  $resource Callback or resource to be stored.
     *
     * @return \Rdthk\DependencyInjection\Container The container.
     */
    public function add($name, $resource)
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

        if (is_callable($resource)) {
            $this->_builders[$name] = $resource;
        } else {
            $this->_values[$name] = $resource;
        }

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
}