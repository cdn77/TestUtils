<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Fixture;

final class ClassWithReadonlyProperty extends ParentClassWithReadonlyProperty
{
    public function __construct(
        private readonly string $readonlyProperty,
        string $parentProperty1,
        string $parentProperty2,
    ) {
        parent::__construct($parentProperty1, $parentProperty2);
    }

    public function readonlyProperty(): string
    {
        return $this->readonlyProperty;
    }
}
