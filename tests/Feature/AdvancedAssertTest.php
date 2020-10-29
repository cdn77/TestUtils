<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Feature;

use Cdn77\TestUtils\Feature\AdvancedAssert;
use Cdn77\TestUtils\Tests\BaseTestCase;
use PHPUnit\Framework\AssertionFailedError;
use Safe\DateTimeImmutable;

final class AdvancedAssertTest extends BaseTestCase
{
    /**
     * @param mixed[] $expected
     * @param mixed[] $actual
     *
     * @dataProvider providerAssertSameWithEqualDateTimesDoesntThrowAssertionFailedError
     */
    public function testAssertSameWithEqualDateTimesDoesntThrowAssertionFailedError(
        array $expected,
        array $actual
    ) : void {
        try {
            AdvancedAssert::assertSameWithEqualDateTimes($expected, $actual);
        } catch (AssertionFailedError $exception) {
            $message = "AssertionFailedError was thrown when it wasn't supposed to be."
                . " Exception message: \n" . $exception->getMessage();
            self::fail($message);
        }
    }

    /** @return mixed[] */
    public function providerAssertSameWithEqualDateTimesDoesntThrowAssertionFailedError() : iterable
    {
        $dateTimeA = new DateTimeImmutable('2017-09-13 12:55:12');
        $dateTimeB = new DateTimeImmutable('2017-09-13 12:55:12');

        yield [
            ['a' => 123, 'b' => 456],
            ['a' => 123, 'b' => 456],
        ];

        yield [
            ['a' => 123, 'b' => $dateTimeA],
            ['a' => 123, 'b' => $dateTimeB],
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789]],
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => 666]]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => 666]]],
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => $dateTimeA]]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => $dateTimeB]]],
        ];
    }

    /**
     * @param mixed[] $expected
     * @param mixed[] $actual
     *
     * @dataProvider providerAssertSameWithEqualDateTimesThrowsAssertionFailedError
     */
    public function testAssertSameWithEqualDateTimesThrowsAssertionFailedError(
        array $expected,
        array $actual,
        string $expectedMessage
    ) : void {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        AdvancedAssert::assertSameWithEqualDateTimes($expected, $actual);
    }

    /** @return mixed[] */
    public function providerAssertSameWithEqualDateTimesThrowsAssertionFailedError() : iterable
    {
        $dateTimeA = new DateTimeImmutable('2017-09-13 12:55:12');
        $dateTimeB = new DateTimeImmutable('2017-09-13 12:55:13');

        yield [
            ['a' => 123, 'b' => 456],
            ['a' => 123, 'c' => 456],
            '~Failed asserting that: array.+The keys are different\.~s',
        ];

        yield [
            ['a' => 123, 'b' => 456],
            ['a' => 123, 'b' => 457],
            '~Failed asserting that: array~',
        ];

        yield [
            ['a' => 123, 'b' => $dateTimeA],
            ['a' => 123, 'b' => $dateTimeB],
            '~Failed asserting that: array~',
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789]],
            ['a' => 123, 'b' => 456],
            '~^Failed asserting that: array.+The keys are different\.~s',
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 123]],
            '~^Failed asserting that part of the array at path array\[c\]~',
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => 666]]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789]],
            '~^Failed asserting that part of the array at path array\[c\].+The keys are different\.~s',
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => 666]]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => []]],
            '~^Failed asserting that part of the array at path array\[c\]\[e\].+The keys are different\.~s',
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => 666]]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => 777]]],
            '~^Failed asserting that part of the array at path array\[c\]\[e\]~',
        ];

        yield [
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => $dateTimeA]]],
            ['a' => 123, 'b' => 456, 'c' => ['d' => 789, 'e' => ['f' => $dateTimeB]]],
            '~^Failed asserting that part of the array at path array\[c\]\[e\]~',
        ];
    }
}
