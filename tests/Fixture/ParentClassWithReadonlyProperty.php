<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Fixture;

class ParentClassWithReadonlyProperty
{
    public function __construct(
        private readonly string $parentReadonlyProperty,
        public readonly string $parentPublicReadonlyProperty,
    ) {
    }

    public function parentReadonlyProperty(): string
    {
        return $this->parentReadonlyProperty;
    }
}
