<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function class_exists;
use function Safe\preg_match;
use function Safe\sprintf;
use function Safe\substr;
use function str_replace;
use function strlen;
use function trait_exists;

final class EveryTestHasSameNamespaceAsTestedClass implements TestCheck
{
    private const PATTERN = '~\* @testedClass (?<targetClass>.+)\n~';

    /** @var iterable<string> $filePathNames */
    private iterable $filePathNames;

    private string $testClassSuffix;

    /** @param iterable<string> $filePathNames */
    public function __construct(iterable $filePathNames, string $testClassSuffix = 'Test')
    {
        $this->filePathNames = $filePathNames;
        $this->testClassSuffix = $testClassSuffix;
    }

    public function run(TestCase $testCaseContext) : void
    {
        $testCaseContext::assertTrue(true);

        foreach ($this->filePathNames as $file) {
            $classReflection = new ReflectionClass(ClassExtractor::get($file));

            $docComment = $classReflection->getDocComment();
            if ($docComment === false) {
                $testCaseContext::fail(
                    sprintf('Test "%s" is missing phpdoc. See other tests for examples', $classReflection->getName())
                );
            }

            preg_match(self::PATTERN, $docComment, $targetClassMatches);

            if ($targetClassMatches !== [] && $targetClassMatches['targetClass'] === 'none') {
                continue;
            }

            $className = $classReflection->getName();
            $classNameWithoutSuffix = substr($className, 0, strlen($this->testClassSuffix) * -1);
            $testedClassName = str_replace('\Tests\\', '\\', $classNameWithoutSuffix);
            if (class_exists($testedClassName) || trait_exists($testedClassName)) {
                continue;
            }

            if (class_exists($classNameWithoutSuffix)) {
                continue;
            }

            if ($targetClassMatches === []) {
                $testCaseContext::fail(
                    sprintf(
                        'Test "%s" is in the wrong namespace, ' .
                        'has name different from tested class or is missing @testedClass annotation',
                        $classReflection->getName()
                    )
                );
            }

            /** @psalm-var class-string $targetClass */
            $targetClass = $targetClassMatches['targetClass'];
            if (class_exists($targetClass)) {
                continue;
            }

            $testCaseContext::fail(
                sprintf(
                    'Test %s is pointing to an non-existing class "%s"',
                    $classReflection->getName(),
                    $targetClassMatches['targetClass']
                )
            );
        }
    }
}
