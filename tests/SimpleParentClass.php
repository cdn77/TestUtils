<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests;

class SimpleParentClass
{
    public function __construct(private string $parentProperty)
    {
    }

    public function getParentProperty(): string
    {
        return $this->parentProperty;
    }
}
