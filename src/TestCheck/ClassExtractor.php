<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\TestCheck;

use League\ConstructFinder\ConstructFinder;
use SplFileInfo;

use function array_filter;
use function array_key_first;
use function str_ends_with;

final class ClassExtractor
{
    /** @return class-string|null */
    public static function extractFromFile(string $filePathName): string|null
    {
        $fileInfo = new SplFileInfo($filePathName);
        $files = array_filter(
            ConstructFinder::locatedIn($fileInfo->getPath())->findClassNames(),
            static fn (string $className) => str_ends_with(
                $className,
                '\\' . $fileInfo->getBasename('.php'),
            ),
        );

        $index = array_key_first($files);
        if ($index === null) {
            return null;
        }

        return $files[$index];
    }
}
