<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Feature;

use Cdn77\TestUtils\Feature\AdvancedAssert;
use Cdn77\TestUtils\Tests\BaseTestCase;
use PHPUnit\Framework\Assert;
use Safe\DateTimeImmutable;
use stdClass;

final class AdvancedAssertAssertCountTest extends BaseTestCase
{
    public function testAssertSameWithEqualDateTimesIncreasesAssertCountBy1() : void
    {
        $now = new DateTimeImmutable();
        AdvancedAssert::assertSameWithEqualDateTimes([$now], [$now]);

        Assert::assertSame(1, Assert::getCount());
    }

    public function testAssertArraysAreSameIncreasesAssertCountBy1() : void
    {
        AdvancedAssert::assertArraysAreSame([], []);

        Assert::assertSame(1, Assert::getCount());
    }

    public function testAssertObjectsAreIdenticalIncreasesAssertCountBy1() : void
    {
        AdvancedAssert::assertObjectsAreIdentical(new stdClass(), new stdClass());

        Assert::assertSame(1, Assert::getCount());
    }
}
