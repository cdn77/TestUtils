<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use Cdn77\EntityFqnExtractor\ClassExtractor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function Safe\preg_match;
use function sprintf;

final class EveryTestHasGroup implements TestCheck
{
    /**
     * @param iterable<string> $filePathNames
     * @param list<string> $allowedGroups
     */
    public function __construct(private iterable $filePathNames, private array $allowedGroups)
    {
    }

    public function run(TestCase $testCaseContext): void
    {
        foreach ($this->filePathNames as $filePathName) {
            $classReflection = new ReflectionClass(ClassExtractor::get($filePathName));
            if ($classReflection->isAbstract()) {
                continue;
            }

            $groupAttributes = $classReflection->getAttributes(Group::class);
            foreach ($groupAttributes as $groupAttribute) {
                $testCaseContext::assertContains(
                    $groupAttribute->getArguments()[0],
                    $this->allowedGroups,
                    sprintf(
                        'Test "%s" has invalid @group annotation "%s"',
                        $classReflection->getName(),
                        $groupAttribute->getArguments()[0],
                    ),
                );
            }

            if ($groupAttributes !== []) {
                continue;
            }

            $this->validateDocComment($testCaseContext, $classReflection);
        }
    }

    /** @param ReflectionClass<object> $reflectionClass */
    private function validateDocComment(TestCase $testCaseContext, ReflectionClass $reflectionClass): void
    {
        $docComment = $reflectionClass->getDocComment();
        if ($docComment === false) {
            $testCaseContext::fail(sprintf('Test "%s" is missing phpdoc comment', $reflectionClass->getName()));
        }

        if (preg_match('~\* @group +(?<group>\w+)(\n| \*/)~', $docComment, $matches) !== 1) {
            $testCaseContext::fail(
                sprintf('Test "%s" is missing @group annotation', $reflectionClass->getName()),
            );
        }

        $testCaseContext::assertContains(
            $matches['group'],
            $this->allowedGroups,
            sprintf(
                'Test "%s" has invalid @group annotation "%s"',
                $reflectionClass->getName(),
                $matches['group'],
            ),
        );
    }
}
