<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests;

use Cdn77\TestUtils\Feature\StubFactory;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    use StubFactory;
}
