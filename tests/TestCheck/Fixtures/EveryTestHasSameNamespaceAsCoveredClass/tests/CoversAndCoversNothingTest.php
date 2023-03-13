<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Tests\TestCheck\Fixtures\EveryTestHasSameNamespaceAsCoveredClass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use stdClass;

#[CoversClass(stdClass::class)]
#[CoversNothing()]
final class CoversAndCoversNothingTest
{
}
