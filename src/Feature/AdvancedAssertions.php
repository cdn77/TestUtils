<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Feature;

use DateTimeInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use TypeError;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function count;
use function get_class;
use function implode;
use function is_array;
use function Safe\sprintf;
use function Safe\substr;
use function strrpos;
use function var_export;

trait AdvancedAssertions
{
    /**
     * @param mixed[] $expected
     * @param mixed[] $actual
     */
    protected static function assertSameWithEqualDateTimes(
        array $expected,
        array $actual,
        ?string $message = null
    ) : void {
        $assertMessage = 'Failed asserting that%s: %s is identical to: %s';
        if ($message === null) {
            $message = $assertMessage;
        } else {
            $message .= "\n" . $assertMessage;
        }

        self::assertSameWithEqualDateTimesRecursive($expected, $actual, $message);
    }

    /**
     * @param mixed[] $expected
     * @param mixed[] $actual
     */
    protected static function assertArraysAreSame(
        array $expected,
        array $actual,
        bool $ignoreOrder = false,
        ?callable $valueAssertionCallback = null
    ) : void {
        foreach ($actual as $key => $value) {
            if (array_key_exists($key, $expected)) {
                continue;
            }

            Assert::assertSame($expected, $actual, sprintf("Arrays aren't same: unexpected key: %s", $key));
        }

        foreach ($expected as $key => $value) {
            if (array_key_exists($key, $actual)) {
                continue;
            }

            Assert::assertSame($expected, $actual, sprintf("Arrays aren't same: missing key: %s", $key));
        }

        if (! $ignoreOrder) {
            Assert::assertSame(
                array_keys($expected),
                array_keys($actual),
                "Arrays aren't same: different order of keys"
            );
        }

        foreach ($expected as $key => $value) {
            try {
                if ($valueAssertionCallback === null) {
                    if (is_array($value) && is_array($actual[$key])) {
                        self::assertArraysAreSame($value, $actual[$key], $ignoreOrder);
                    } else {
                        Assert::assertSame($value, $actual[$key]);
                    }

                    continue;
                }

                $valueAssertionCallback($value, $actual[$key], $key);
            } catch (TypeError $error) {
                Assert::assertSame(
                    $expected,
                    $actual,
                    sprintf(
                        "Arrays aren't same: value of unexpected type given for key \"%s\"\n%s\nGiven value: %s",
                        $key,
                        $error->getMessage(),
                        var_export($actual[$key])
                    )
                );
            } catch (AssertionFailedError $error) {
                $message = $error->toString();

                if ($error instanceof ExpectationFailedException && $error->getComparisonFailure() !== null) {
                    $message .= $error->getComparisonFailure()->getDiff();
                }

                Assert::assertSame(
                    $expected,
                    $actual,
                    sprintf(
                        "Arrays aren't same: assertion failed for value of key \"%s\"\n%s",
                        $key,
                        $message
                    )
                );
            }
        }
    }

    protected static function assertObjectsAreIdentical(object $expected, object $actual) : void
    {
        if ($expected === $actual) {
            return;
        }

        $actualClass = get_class($actual);
        $expectedClass = get_class($expected);
        if ($expectedClass !== $actualClass) {
            Assert::assertSame(
                $expected,
                $actual,
                sprintf('Objects are not the same; expected class %s, got %s', $expectedClass, $actualClass)
            );
        }

        $actualArray = (array) $actual;
        foreach ((array) $expected as $key => $value) {
            if ($value === $actualArray[$key]) {
                continue;
            }

            $nullBytePos = strrpos($key, "\0");
            Assert::assertSame(
                $expected,
                $actual,
                sprintf(
                    'Objects are not the same; property "%s" is expected to be "%s", got "%s"',
                    substr($key, $nullBytePos === false ? 0 : $nullBytePos),
                    var_export($value, true),
                    var_export($actualArray[$key], true)
                )
            );
        }
    }

    /**
     * @param array<string, mixed> $expected
     * @param mixed[] $actual
     * @param string[] $keys
     */
    private static function assertSameWithEqualDateTimesRecursive(
        array $expected,
        array $actual,
        string $message,
        array $keys = []
    ) : void {
        if (array_keys($expected) !== array_keys($actual)) {
            $formattedMessage = self::formatAssertMessage($message, $keys, $expected, $actual);
            $formattedMessage .= "\nThe keys are different.";
            Assert::assertEquals($expected, $actual, $formattedMessage);
        }

        foreach ($expected as $key => $value) {
            if (is_array($value)) {
                self::assertSameWithEqualDateTimesRecursive(
                    $value,
                    $actual[$key],
                    $message,
                    array_merge($keys, [$key])
                );
            } elseif ($value instanceof DateTimeInterface) {
                $formattedMessage = self::formatAssertMessage($message, $keys, $expected, $actual);
                Assert::assertEquals($value, $actual[$key], $formattedMessage);
            } else {
                $formattedMessage = self::formatAssertMessage($message, $keys, $expected, $actual);
                Assert::assertSame($value, $actual[$key], $formattedMessage);
            }
        }
    }

    /**
     * @param string[] $keys
     * @param mixed[] $expected
     * @param mixed[] $actual
     */
    private static function formatAssertMessage(string $message, array $keys, array $expected, array $actual) : string
    {
        if (count($keys) === 0) {
            $path = '';
        } else {
            $path = ' part of the array at path array[' . implode('][', $keys) . ']';
        }

        return sprintf($message, $path, var_export($actual, true), var_export($expected, true));
    }
}
