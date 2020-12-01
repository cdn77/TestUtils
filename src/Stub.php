<?php

declare(strict_types=1);

namespace Cdn77\TestUtils;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

use function array_merge;
use function get_class;
use function Safe\sprintf;

final class Stub
{
    /**
     * @param array<string, mixed> $properties
     *
     * @template T of object
     * @psalm-param class-string<T> $class
     * @psalm-return T
     */
    public static function create(string $class, array $properties = []) : object
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

    /**
     * @param array<string, mixed> $newProperties
     *
     * @template T of object
     * @psalm-param T $stub
     * @psalm-return T
     */
    public static function extend(object $stub, array $newProperties = []) : object
    {
        $class = get_class($stub);
        $reflection = new ReflectionClass($class);

        $properties = [];
        foreach (self::getAllProperties($reflection) as $property) {
            $property->setAccessible(true);
            if (! $property->isInitialized($stub)) {
                continue;
            }

            $properties[$property->getName()] = $property->getValue($stub);
        }

        return self::create($class, array_merge($properties, $newProperties));
    }

    /**
     * @template T of object
     * @psalm-param ReflectionClass<T> $class
     */
    private static function getClosestProperty(ReflectionClass $class, string $property) : ?ReflectionProperty
    {
        if ($class->hasProperty($property)) {
            return $class->getProperty($property);
        }

        /**
         * @template T of object
         * @psalm-var ReflectionClass<T>|false $parentClass
         */
        $parentClass = $class->getParentClass();
        if ($parentClass === false) {
            return null;
        }

        return self::getClosestProperty($parentClass, $property);
    }

    /**
     * @param ReflectionClass<object> $reflection
     * @param ReflectionProperty[] $properties
     *
     * @return ReflectionProperty[]
     */
    private static function getAllProperties(ReflectionClass $reflection, array $properties = []) : array
    {
        $properties = array_merge($reflection->getProperties(), $properties);
        $parent = $reflection->getParentClass();
        if ($parent === false) {
            return $properties;
        }

        return self::getAllProperties($parent, $properties);
    }
}
