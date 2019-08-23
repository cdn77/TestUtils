<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Utils\PHPStan\Extension;

use Cdn77\TestUtils\PHPStan\CreateStubExtension;
use Cdn77\TestUtils\Tests\BaseTestCase;

final class CreateStub extends CreateStubExtension
{
    public function getClass() : string
    {
        return BaseTestCase::class;
    }
}
