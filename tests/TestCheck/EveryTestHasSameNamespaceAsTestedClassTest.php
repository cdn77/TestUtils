<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\TestCheck;

use Cdn77\TestUtils\TestCheck\EveryTestHasSameNamespaceAsTestedClass;
use Cdn77\TestUtils\Tests\BaseTestCase;
use Generator;
use PHPUnit\Framework\AssertionFailedError;

final class EveryTestHasSameNamespaceAsTestedClassTest extends BaseTestCase
{
    /** @dataProvider providerSuccess */
    public function testSuccess(string $filePath): void
    {
        $check = new EveryTestHasSameNamespaceAsTestedClass(
            [__DIR__ . '/Fixtures/EveryTestHasSameNamespaceAsTestedClass/tests/' . $filePath],
            'Tests',
        );
        $check->run($this);
    }

    /** @return Generator<array-key, list<string>> */
    public static function providerSuccess(): Generator
    {
        yield ['SameNamespaceTest.php'];
        yield ['SameNamespaceLinkedTest.php'];
        yield ['NoLinkTest.php'];
    }

    /** @dataProvider providerFail */
    public function testFail(string $filePath, string $error): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage($error);

        $check = new EveryTestHasSameNamespaceAsTestedClass(
            [__DIR__ . '/Fixtures/EveryTestHasSameNamespaceAsTestedClass/tests/' . $filePath],
            'Tests',
        );
        $check->run($this);
    }

    /** @return Generator<array-key, list<string>> */
    public static function providerFail(): Generator
    {
        yield [
            'MissingAnnotationsTest.php',
            'is in the wrong namespace, has name different from tested class or is missing @testedClass annotation',
        ];

        yield [
            'NonexistentLinkTest.php',
            'is pointing to an non-existing class',
        ];
    }
}
