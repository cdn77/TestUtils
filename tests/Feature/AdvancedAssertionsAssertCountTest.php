<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Feature;

use Cdn77\TestUtils\Feature\AdvancedAssertions;
use Cdn77\TestUtils\Tests\BaseTestCase;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use stdClass;

final class AdvancedAssertionsAssertCountTest extends BaseTestCase
{
    use AdvancedAssertions;

    public function testAssertSameWithEqualDateTimesIncreasesAssertCountBy1() : void
    {
        $now = new DateTimeImmutable();
        self::assertSameWithEqualDateTimes([$now], [$now]);

        Assert::assertSame(1, Assert::getCount());
    }

    public function testAssertArraysAreSameIncreasesAssertCountBy1() : void
    {
        self::assertArraysAreSame([], []);

        Assert::assertSame(1, Assert::getCount());
    }

    public function testAssertObjectsAreIdenticalIncreasesAssertCountBy1() : void
    {
        self::assertObjectsAreIdentical(new stdClass(), new stdClass());

        Assert::assertSame(1, Assert::getCount());
    }
}
