<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\TestCheck;

use Cdn77\TestUtils\TestCheck\EveryTestIsFinal;
use Cdn77\TestUtils\Tests\BaseTestCase;
use PHPUnit\Framework\ExpectationFailedException;

final class EveryTestIsFinalTest extends BaseTestCase
{
    public function testSuccess() : void
    {
        $check = new EveryTestIsFinal([__DIR__ . '/Fixtures/FinalClass.php']);
        $check->run($this);
    }

    public function testFail() : void
    {
        try {
            $check = new EveryTestIsFinal([__DIR__ . '/Fixtures/NotFinalClass.php']);
            $check->run($this);
        } catch (ExpectationFailedException $exception) {
            return;
        }

        self::fail('Unexpected check outcome');
    }
}
