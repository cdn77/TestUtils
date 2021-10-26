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
use function strlen;
use function strpos;
use function substr_replace;
use function trait_exists;

final class EveryTestHasSameNamespaceAsTestedClass implements TestCheck
{
    private const PATTERN = '~\* @testedClass (?<targetClass>.+?)(?:\n| \*/)~';

    /** @var iterable<string> $filePathNames */
    private iterable $filePathNames;

    private string $testsNamespaceSuffix;

    /** @param iterable<string> $filePathNames */
    public function __construct(iterable $filePathNames, string $testsNamespaceSuffix = 'Tests')
    {
        $this->filePathNames = $filePathNames;
        $this->testsNamespaceSuffix = '\\' . $testsNamespaceSuffix . '\\';
    }

    public function run(TestCase $testCaseContext) : void
    {
        $testCaseContext::assertTrue(true);

        foreach ($this->filePathNames as $file) {
            $classReflection = new ReflectionClass(ClassExtractor::get($file));

            $docComment = $classReflection->getDocComment();
            if ($docComment === false) {
                $docComment = '';
            }

            preg_match(self::PATTERN, $docComment, $targetClassMatches);

            if ($targetClassMatches !== [] && $targetClassMatches['targetClass'] === 'none') {
                continue;
            }

            $className = $classReflection->getName();
            $classNameWithoutSuffix = substr($className, 0, -4);
            $pos = strpos($classNameWithoutSuffix, $this->testsNamespaceSuffix);
            if ($pos === false) {
                $testedClassName = $classNameWithoutSuffix;
            } else {
                $testedClassName = substr_replace(
                    $classNameWithoutSuffix,
                    '\\',
                    $pos,
                    strlen($this->testsNamespaceSuffix)
                );
            }

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
