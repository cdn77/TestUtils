<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function class_exists;
use function Safe\preg_match;
use function Safe\preg_match_all;
use function sprintf;
use function strlen;
use function strpos;
use function substr;
use function substr_replace;
use function trait_exists;

final class EveryTestHasSameNamespaceAsCoveredClass implements TestCheck
{
    private const PatternCovers = '~\* @covers(DefaultClass)? +(?<coveredClass>.+?)(?:\n| \*/)~';
    private const PatternCoversNothing = '~\* @coversNothing~';

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

            $docComment = $classReflection->getDocComment();
            if ($docComment === false) {
                $docComment = '';
            }

            $matchesCovers = preg_match_all(self::PatternCovers, $docComment, $coversMatches) > 0;
            $matchesCoversNothing = preg_match(self::PatternCoversNothing, $docComment) === 1;

            if ($matchesCovers && $matchesCoversNothing) {
                $testCaseContext::fail(sprintf(
                    'Test file "%s" contains both @covers and @coversNothing annotations.',
                    $file,
                ));
            }

            if ($matchesCoversNothing) {
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

            if (class_exists($classNameWithoutSuffix)) {
                continue;
            }

            if ($coversMatches[0] !== []) {
                continue;
            }

            $testCaseContext::fail(
                sprintf(
                    'Test "%s" is in the wrong namespace, ' .
                    'has name different from tested class or is missing @covers annotation',
                    $classReflection->getName(),
                ),
            );
        }
    }
}
