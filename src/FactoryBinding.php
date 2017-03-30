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
 *
 */
class FactoryBinding implements Binding
{
    private $factory;

    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    public function build($container)
    {
        $ref = new \ReflectionFunction($this->factory);
        $parameters = $ref->getParameters();
        $arguments = [];
        $containerPassed = false;

        foreach ($parameters as $parameter) {
            if ($parameter->hasType()) {
                $arguments[] = $container->get((string) $parameter->getType());
            } else {
                if ($containerPassed) {
                    $pName = $parameter->getName();
                    throw new \TypeError(
                        "Factory parameter '{$pName}' is missing a type hint."
                    );
                }

                $arguments[] = $container;
                $containerPassed = true;
            }
        }

        return call_user_func_array($this->factory, $arguments);
    }
}
