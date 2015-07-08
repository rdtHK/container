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
     * Adds a new value to the container.
     *
     * If a callable was passed as the $value parameter,
     * when this element is first retrieved, it will be
     * called with the container as a parameter and the
     * return value cached.
     *
     * @param string $key   Name associated with the value.
     * @param mixed  $value Callback or value to be stored.
     *
     * @return void
     */
    public function add($key, $value)
    {
        if (isset($this->_builders[$key]) || isset($this->_values[$key])) {
            throw new \InvalidArgumentException("Key '$key' is already present.");
        }

        if (!is_string($key)) {
            $keyType = gettype($key);
            throw new \InvalidArgumentException(
                "'$keyType' is not a valid key type."
            );
        }

        if (is_callable($value)) {
            $this->_builders[$key] = $value;
        }

        $this->_values[$key] = $value;
    }

    /**
     * Returns the value associated with the supplied key.
     *
     * @param string $key Name associated with the value.
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!empty($this->_builders[$key])) {
            $this->_values[$key] = $this->_builders[$key]($this);
            unset($this->_builders[$key]);
        }

        if (empty($this->_values[$key])) {
            throw new \InvalidArgumentException("Key '$key' was not added.");
        }

        return $this->_values[$key];
    }
}