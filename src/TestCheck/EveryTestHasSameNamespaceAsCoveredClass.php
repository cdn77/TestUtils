<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function class_exists;
use function sprintf;
use function strlen;
use function strpos;
use function substr;
use function substr_replace;
use function trait_exists;

final class EveryTestHasSameNamespaceAsCoveredClass implements TestCheck
{
    private string $testsNamespaceSuffix;

    /** @param iterable<string> $filePathNames */
    public function __construct(private iterable $filePathNames, string $testsNamespaceSuffix = 'Tests')
    {
        $this->testsNamespaceSuffix = '\\' . $testsNamespaceSuffix . '\\';
    }

    public function run(TestCase $testCaseContext): void
    {
        $testCaseContext::assertTrue(true);

        foreach ($this->filePathNames as $file) {
            $classReflection = new ReflectionClass(ClassExtractor::get($file));

            $attributesCoversClass = $classReflection->getAttributes(CoversClass::class);
            $attributesCoversNothing = $classReflection->getAttributes(CoversNothing::class);

            $hasCovers = $attributesCoversClass !== [];
            $hasCoversNothing = $attributesCoversNothing !== [];

            if ($hasCovers && $hasCoversNothing) {
                $testCaseContext::fail(sprintf(
                    'Specifying CoversClass and CoversNothing attributes at the same time makes no sense (in "%s").',
                    $file,
                ));
            }

            if ($hasCoversNothing || $hasCovers) {
                continue;
            }

            $className = $classReflection->getName();
            $classNameWithoutSuffix = substr($className, 0, -4);
            $pos = strpos($classNameWithoutSuffix, $this->testsNamespaceSuffix);
            if ($pos === false) {
                $coveredClassName = $classNameWithoutSuffix;
            } else {
                $coveredClassName = substr_replace(
                    $classNameWithoutSuffix,
                    '\\',
                    $pos,
                    strlen($this->testsNamespaceSuffix),
                );
            }

            if (class_exists($coveredClassName) || trait_exists($coveredClassName)) {
                continue;
            }

            $testCaseContext::fail(
                sprintf(
                    'Test "%s" is in the wrong namespace, ' .
                    'has name different from tested class or is missing CoversClass attribute',
                    $classReflection->getName(),
                ),
            );
        }
    }
}
