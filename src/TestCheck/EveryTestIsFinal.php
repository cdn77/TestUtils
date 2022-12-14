<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function Safe\sprintf;

final class EveryTestIsFinal implements TestCheck
{
    /** @param iterable<string> $filePathNames */
    public function __construct(private iterable $filePathNames)
    {
    }

    public function run(TestCase $testCaseContext): void
    {
        foreach ($this->filePathNames as $filePathName) {
            $classReflection = new ReflectionClass(ClassExtractor::get($filePathName));
            $testCaseContext::assertTrue(
                $classReflection->isFinal(),
                sprintf('Test %s is missing "final" class keyword', $classReflection->getName()),
            );
        }
    }
}
