<?php

declare(strict_types=1);

namespace Cdn77\TestUtils;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

use function array_merge;
use function get_class;
use function sprintf;

final class Stub
{
    /**
     * @param array<string, mixed> $properties
     * @param class-string<T> $class
     *
     * @return T
     *
     * @template T of object
     */
    public static function create(string $class, array $properties = []): object
    {
        $reflection = new ReflectionClass($class);

        $stub = $reflection->newInstanceWithoutConstructor();
        /** @var mixed $value */
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
     * @param T $stub
     *
     * @return T
     *
     * @template T of object
     */
    public static function extend(object $stub, array $newProperties = []): object
    {
        // phpstan has problem with analyzing this still
        // phpcs:ignore SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaFunctionCall
        $class = get_class($stub);
        $reflection = new ReflectionClass($class);

        /** @var array<string, mixed> $properties */
        $properties = [];
        foreach (self::getAllProperties($reflection) as $property) {
            $property->setAccessible(true);
            if (! $property->isInitialized($stub)) {
                continue;
            }

            /** @var mixed $value */
            $value = $property->getValue($stub);
            $properties[$property->getName()] = $value;
        }

        return self::create($class, array_merge($properties, $newProperties));
    }

    /**
     * @param ReflectionClass<T> $class
     *
     * @template T of object
     */
    private static function getClosestProperty(ReflectionClass $class, string $property): ReflectionProperty|null
    {
        if ($class->hasProperty($property)) {
            return $class->getProperty($property);
        }

        /**
         * @template T of object
         * @var ReflectionClass<T>|false $parentClass
         */
        $parentClass = $class->getParentClass();
        if ($parentClass === false) {
            return null;
        }

        return self::getClosestProperty($parentClass, $property);
    }

    /**
     * @param ReflectionClass<T> $reflection
     * @param ReflectionProperty[] $properties
     *
     * @return ReflectionProperty[]
     *
     * @template T of object
     */
    private static function getAllProperties(ReflectionClass $reflection, array $properties = []): array
    {
        $properties = array_merge($reflection->getProperties(), $properties);

        /**
         * @psalm-template T of object
         * @psalm-var ReflectionClass<T>|false $parentClass
         */
        $parentClass = $reflection->getParentClass();
        if ($parentClass === false) {
            return $properties;
        }

        return self::getAllProperties($parentClass, $properties);
    }
}
