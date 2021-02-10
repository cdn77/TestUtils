<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests;

class SimpleParentClass
{
    private string $parentProperty;

    public function __construct(string $parentProperty)
    {
        $this->parentProperty = $parentProperty;
    }

    public function getParentProperty() : string
    {
        return $this->parentProperty;
    }
}
