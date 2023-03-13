<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\TestCheck;

use Cdn77\TestUtils\TestCheck\EveryTestHasGroup;
use Cdn77\TestUtils\Tests\BaseTestCase;
use Generator;
use PHPUnit\Framework\AssertionFailedError;

final class EveryTestHasGroupTest extends BaseTestCase
{
    /**
     * @param non-empty-list<string>|null $requiredGroups
     * @param list<string>|null $supportedGroups
     *
     * @dataProvider providerSuccess
     */
    public function testSuccess(
        string $filePath,
        array|null $requiredGroups,
        array|null $supportedGroups,
    ): void {
        $check = new EveryTestHasGroup([__DIR__ . '/Fixtures/' . $filePath], $requiredGroups, $supportedGroups);
        $check->run($this);
    }

    /** @return Generator<string, array{string, non-empty-list<string>|null, list<string>|null}> */
    public static function providerSuccess(): Generator
    {
        yield 'required, any supported' => ['WithGroups.php', ['a'], null];
        yield 'required, supported' => ['WithGroups.php', ['a'], ['b']];
        yield 'no required, supported' => ['WithGroups.php', null, ['a', 'b']];
        yield 'no required, any supported' => ['WithGroups.php', null, null];
    }

    /**
     * @param non-empty-list<string>|null $requiredGroups
     * @param list<string>|null $supportedGroups
     *
     * @dataProvider providerFail
     */
    public function testFail(
        string $filePath,
        array|null $requiredGroups,
        array|null $supportedGroups,
    ): void {
        try {
            $check = new EveryTestHasGroup([__DIR__ . '/Fixtures/' . $filePath], $requiredGroups, $supportedGroups);
            $check->run($this);
        } catch (AssertionFailedError) {
            return;
        }

        self::fail('Unexpected check outcome');
    }

    /** @return Generator<string, array{string, non-empty-list<string>|null, list<string>|null}> */
    public static function providerFail(): Generator
    {
        yield 'has unsupported group, required' => ['WithGroups.php', null, []];
        yield 'has no group, required' => ['WithoutGroup.php', ['a'], null];
    }
}
