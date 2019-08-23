<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Feature;

final class SimpleClass extends SimpleParentClass
{
    /** @var string */
    private $property1;

    /** @var string */
    private $property2;

    /** @var string */
    private $propertyWithDefaultValue = 'default value';

    public function __construct(string $property1, string $property2, string $property3)
    {
        $this->property1 = $property1;
        $this->property2 = $property2;

        parent::__construct($property3);
    }

    public function getProperty1() : string
    {
        return $this->property1;
    }

    public function getProperty2() : string
    {
        return $this->property2;
    }

    public function getPropertyWithDefaultValue() : string
    {
        return $this->propertyWithDefaultValue;
    }
}
