<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Feature;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use function Safe\sprintf;

trait StubFactory
{
    private static function getClosestProperty(ReflectionClass $class, string $property) : ?ReflectionProperty
    {
        if ($class->hasProperty($property)) {
            return $class->getProperty($property);
        }

        $parentClass = $class->getParentClass();
        if ($parentClass === false) {
            return null;
        }

        return self::getClosestProperty($parentClass, $property);
    }

    /**
     * @param mixed[] $properties
     */
    protected function createStub(string $class, array $properties = []) : object
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
