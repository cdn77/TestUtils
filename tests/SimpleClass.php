<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests;

final class SimpleClass extends SimpleParentClass
{
    private string $propertyWithDefaultValue = 'default value';

    public function __construct(private string $property1, private string $property2, string $property3)
    {
        parent::__construct($property3);
    }

    public function getProperty1(): string
    {
        return $this->property1;
    }

    public function getProperty2(): string
    {
        return $this->property2;
    }

    public function getPropertyWithDefaultValue(): string
    {
        return $this->propertyWithDefaultValue;
    }
}
