<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\TestCheck;

use Cdn77\TestUtils\TestCheck\EveryTestHasGroup;
use Cdn77\TestUtils\Tests\BaseTestCase;
use Generator;
use PHPUnit\Framework\AssertionFailedError;

final class EveryTestHasGroupTest extends BaseTestCase
{
    public function testSuccess() : void
    {
        $check = new EveryTestHasGroup([__DIR__ . '/Fixtures/WithGroup.php'], ['unit']);
        $check->run($this);
    }

    /** @dataProvider providerFail */
    public function testFail(string $filePath) : void
    {
        try {
            $check = new EveryTestHasGroup([__DIR__ . '/Fixtures/' . $filePath], ['unit']);
            $check->run($this);
        } catch (AssertionFailedError $exception) {
            return;
        }

        self::fail('Unexpected check outcome');
    }

    /** @return Generator<list<string>> */
    public function providerFail() : Generator
    {
        yield ['WithoutGroup.php'];
        yield ['WithUnlistedGroup.php'];
    }
}
