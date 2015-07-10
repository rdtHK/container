<?php

namespace Rdthk\DependencyInjection\Tests\Mocks;

class InjectionTestClass
{
    private $constructorValue;
    private $property;

    public function __construct($constructorValue) {
        $this->constructorValue = $constructorValue;
    }

    public function setProperty($property) {
        $this->property = $property;
    }

    public function getConstructorValue() {
        return $this->constructorValue;
    }

    public function getProperty() {
        return $this->property;
    }
}