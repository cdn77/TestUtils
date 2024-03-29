<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\TestCheck;

use Cdn77\TestUtils\TestCheck\EveryTestHasSameNamespaceAsCoveredClass;
use Cdn77\TestUtils\Tests\BaseTestCase;
use Generator;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;

final class EveryTestHasSameNamespaceAsCoveredClassTest extends BaseTestCase
{
    #[DataProvider('providerSuccess')]
    public function testSuccess(string $filePath): void
    {
        $check = new EveryTestHasSameNamespaceAsCoveredClass(
            [__DIR__ . '/Fixtures/EveryTestHasSameNamespaceAsCoveredClass/tests/' . $filePath],
            'Tests',
        );
        $check->run($this);
    }

    /** @return Generator<array-key, list<string>> */
    public static function providerSuccess(): Generator
    {
              $files = [
                  'IgnoreMultipleCoversTest.php',
                  'SameNamespaceTest.php',
                  'SameNamespaceAsLinkedCoveredClassTest.php',
                  'CoveredClassWithSomeWhitespaceTest.php',
                  'CoversNothingTest.php',
              ];

              foreach ($files as $file) {
                  yield $file => [$file];
              }
    }

    #[DataProvider('providerFail')]
    public function testFail(string $filePath, string $error): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage($error);

        $check = new EveryTestHasSameNamespaceAsCoveredClass(
            [__DIR__ . '/Fixtures/EveryTestHasSameNamespaceAsCoveredClass/tests/' . $filePath],
            'Tests',
        );
        $check->run($this);
    }

    /** @return Generator<array-key, list<string>> */
    public static function providerFail(): Generator
    {
        yield [
            'CoversAndCoversNothingTest.php',
            'Specifying CoversClass and CoversNothing attributes at the same time',
        ];

        yield [
            'SubNamespace/SameNamespaceTest.php',
            'is in the wrong namespace',
        ];

        yield [
            'SameNamespaceWrongNameTest.php',
            'is in the wrong namespace',
        ];
    }
}
