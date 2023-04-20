<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function sprintf;

final class EveryTestIsFinal implements TestCheck
{
    /** @param iterable<string> $filePathNames */
    public function __construct(private iterable $filePathNames)
    {
    }

    public function run(TestCase $testCaseContext): void
    {
        foreach ($this->filePathNames as $filePathName) {
            $className = ClassExtractor::extractFromFile($filePathName);
            if ($className === null) {
                $testCaseContext::fail(sprintf('No class found in file "%s"', $filePathName));
            }

            $classReflection = new ReflectionClass($className);

            $testCaseContext::assertTrue(
                $classReflection->isFinal(),
                sprintf('Test %s is missing "final" class keyword', $classReflection->getName()),
            );
        }
    }
}
