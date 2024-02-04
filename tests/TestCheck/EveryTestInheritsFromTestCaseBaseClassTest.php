<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\TestCheck;

use Cdn77\TestUtils\TestCheck\EveryTestInheritsFromTestCaseBaseClass;
use Cdn77\TestUtils\Tests\BaseTestCase;
use Generator;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;

final class EveryTestInheritsFromTestCaseBaseClassTest extends BaseTestCase
{
    #[DataProvider('providerSuccess')]
    public function testSuccess(string $filePath): void
    {
        $check = new EveryTestInheritsFromTestCaseBaseClass(
            [__DIR__ . '/Fixtures/' . $filePath],
            BaseTestCase::class,
        );
        $check->run($this);
    }

    /** @return Generator<list<string>> */
    public static function providerSuccess(): Generator
    {
        yield ['ExtendsBase.php'];
        yield ['ExtendsBaseUsingParent.php'];
        yield ['../../BaseTestCase.php'];
    }

    #[DataProvider('providerFail')]
    public function testFail(string $filePath): void
    {
        try {
            $check = new EveryTestInheritsFromTestCaseBaseClass(
                [__DIR__ . '/Fixtures/' . $filePath],
                BaseTestCase::class,
            );
            $check->run($this);
        } catch (AssertionFailedError) {
            return;
        }

        self::fail('Unexpected check outcome');
    }

    /** @return Generator<list<string>> */
    public static function providerFail(): Generator
    {
        yield ['DoesNotExtendAnything.php'];
        yield ['DoesNotExtendBase.php'];
    }
}
