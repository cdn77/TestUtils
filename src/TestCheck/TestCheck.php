<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use PHPUnit\Framework\TestCase;

interface TestCheck
{
    public function run(TestCase $testCaseContext) : void;
}
