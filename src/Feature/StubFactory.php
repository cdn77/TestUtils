<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Feature;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

use function Safe\sprintf;

trait StubFactory
{
    /**
     * @phpstan-template T of object
     * @phpstan-param    ReflectionClass<T> $class
     */
    private static function getClosestProperty(ReflectionClass $class, string $property) : ?ReflectionProperty
    {
        if ($class->hasProperty($property)) {
            return $class->getProperty($property);
        }

        /**
         * @phpstan-template T of object
         * @phpstan-var ReflectionClass<T>|false $parentClass
         */
        $parentClass = $class->getParentClass();
        if ($parentClass === false) {
            return null;
        }

        return self::getClosestProperty($parentClass, $property);
    }

    /**
     * @param array<mixed> $properties
     *
     * @phpstan-template T of object
     * @phpstan-param    class-string<T> $class
     * @phpstan-return   T
     */
    protected function makeStub(string $class, array $properties = []) : object
    {
        $reflection = new ReflectionClass($class);

        $stub = $reflection->newInstanceWithoutConstructor();
        foreach ($properties as $property => $value) {
            $reflectionProperty = self::getClosestProperty($reflection, $property);
            if ($reflectionProperty === null) {
                throw new ReflectionException(sprintf('Property "%s" not found', $property));
            }

            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($stub, $value);
        }

        return $stub;
    }
}
