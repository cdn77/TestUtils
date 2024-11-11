<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function array_intersect;
use function array_map;
use function in_array;
use function sprintf;

final class EveryTestHasGroup implements TestCheck
{
    /**
     * @param iterable<string> $filePathNames
     * @param non-empty-list<string>|null $requiredGroups
     * @param list<string> $supportedGroups
     */
    public function __construct(
        private iterable $filePathNames,
        private array|null $requiredGroups = null,
        private array|null $supportedGroups = null,
    ) {
        if (
            $requiredGroups !== null
            && $supportedGroups !== null
            && array_intersect($requiredGroups, $supportedGroups) !== []
        ) {
            throw new InvalidArgumentException('Required groups must not be in supported groups');
        }
    }

    public function run(TestCase $testCaseContext): void
    {
        foreach ($this->filePathNames as $filePathName) {
            $className = ClassExtractor::extractFromFile($filePathName);
            if ($className === null) {
                $testCaseContext::fail(sprintf('No class found in file "%s"', $filePathName));
            }

            $classReflection = new ReflectionClass($className);
            if ($classReflection->isAbstract()) {
                continue;
            }

            /** @var array<string> $groups */
            $groups = array_map(
                static fn ($groupAttribute) => $groupAttribute->getArguments()[0],
                $classReflection->getAttributes(Group::class),
            );

            $hasRequiredGroup = false;
            foreach ($groups as $group) {
                if (
                    $this->requiredGroups !== null
                    && in_array($group, $this->requiredGroups, true)
                ) {
                    $hasRequiredGroup = true;

                    continue;
                }

                if ($this->supportedGroups === null) {
                    continue;
                }

                $testCaseContext::assertContains(
                    $group,
                    $this->supportedGroups,
                    sprintf(
                        'Test "%s" has invalid Group attribute "%s"',
                        $classReflection->getName(),
                        $group,
                    ),
                );
            }

            if ($this->requiredGroups !== null) {
                $testCaseContext::assertTrue(
                    $hasRequiredGroup,
                    sprintf(
                        'Test "%s" does not have required Group attribute',
                        $classReflection->getName(),
                    ),
                );
            } else {
                $testCaseContext::assertTrue(true);
            }
        }
    }
}
