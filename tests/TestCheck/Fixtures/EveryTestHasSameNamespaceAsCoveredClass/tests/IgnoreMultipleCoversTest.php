<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Tests\TestCheck\Fixtures\EveryTestHasSameNamespaceAsCoveredClass;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass('\Cdn77\TestUtils\Tests\TestCheck\Fixtures\EveryTestHasSameNamespaceAsTestedClass\A')]
#[CoversClass('\Cdn77\TestUtils\Tests\TestCheck\Fixtures\EveryTestHasSameNamespaceAsTestedClass\B')]
final class IgnoreMultipleCoversTest
{
}
