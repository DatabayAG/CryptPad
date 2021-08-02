<?php

declare(strict_types=1);

namespace CryptPad\Controller;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class RepositoryObject.
 */
abstract class RepositoryObject extends Base
{
    abstract public function getObjectGuiClass() : string;

    abstract public function getObjectGuiConstructorParams() : array;

    protected function beforeLocatorIsBuild() : void
    {
    }

    /**
     * @throws ReflectionException
     */
    protected function drawHeader() : void
    {
        $class = $this->getObjectGuiClass();
        $reflClass = new ReflectionClass($class);
        $object = $reflClass->newInstanceArgs($this->getObjectGuiConstructorParams());

        $reflectionMethod = new ReflectionMethod($class, 'setTitleAndDescription');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($object);

        $this->dic['ilLocator']->addRepositoryItems($this->getRefId());
        $this->beforeLocatorIsBuild();

        $this->pageTemplate->setLocator();
    }
}
