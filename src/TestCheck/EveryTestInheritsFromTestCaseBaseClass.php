<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function Safe\sprintf;

final class EveryTestInheritsFromTestCaseBaseClass implements TestCheck
{
    /**
     * @param iterable<string> $filePathNames
     * @param class-string<TestCase> $testCaseBaseClass
     */
    public function __construct(private iterable $filePathNames, private string $testCaseBaseClass)
    {
    }

    public function run(TestCase $testCaseContext): void
    {
        $testCaseContext::assertTrue(true);

        foreach ($this->filePathNames as $filePathName) {
            $classReflection = new ReflectionClass(ClassExtractor::get($filePathName));

            /** @psalm-var ReflectionClass<TestCase>|false $parentClassReflection */
            $parentClassReflection = $classReflection->getParentClass();
            if ($parentClassReflection === false) {
                $testCaseContext::fail(
                    sprintf(
                        'Test "%s" does not extend any class, use "%s" as the base class',
                        $classReflection->getName(),
                        $this->testCaseBaseClass
                    )
                );
            }

            if ($classReflection->getName() === $this->testCaseBaseClass) {
                continue;
            }

            $this->assertParentClass($testCaseContext, $classReflection, $parentClassReflection);
        }
    }

    /**
     * @param ReflectionClass<object> $classReflection
     * @param ReflectionClass<TestCase> $parentClassReflection
     */
    private function assertParentClass(
        TestCase $testCaseContext,
        ReflectionClass $classReflection,
        ReflectionClass $parentClassReflection,
    ): void {
        if ($parentClassReflection->getName() === $this->testCaseBaseClass) {
            return;
        }

            /** @psalm-var ReflectionClass<TestCase>|false $parentClassReflection */
        $parentClassReflection = $parentClassReflection->getParentClass();
        if ($parentClassReflection === false) {
            $testCaseContext::fail(
                sprintf(
                    'Test "%s" is extending different class than expected, use "%s" as the base class',
                    $classReflection->getName(),
                    $this->testCaseBaseClass
                )
            );
        }

        $this->assertParentClass($testCaseContext, $classReflection, $parentClassReflection);
    }
}
