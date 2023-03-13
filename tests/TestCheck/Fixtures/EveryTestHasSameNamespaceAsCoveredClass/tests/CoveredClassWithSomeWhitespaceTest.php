<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Tests\TestCheck\Fixtures\EveryTestHasSameNamespaceAsCoveredClass;

use Cdn77\TestUtils\Tests\TestCheck\Fixtures\EveryTestHasSameNamespaceAsCoveredClass\SameNamespace;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SameNamespace::class)]
final class CoveredClassWithSomeWhitespaceTest
{
}
