# Cdn77 TestUtils

[![GitHub Actions][GA Image]][GA Link]
[![Code Coverage][Coverage Image]][CodeCov Link]
[![Downloads][Downloads Image]][Packagist Link]
[![Packagist][Packagist Image]][Packagist Link]
[![Infection MSI][Infection Image]][Infection Link]

## Contents

- [Installation](#installation)
- [Features](#features)
  - [Stub](#stub)
  - [Test Checks](#test-checks)

## Installation

* Require this project as composer dev dependency:

```
composer require --dev cdn77/test-utils
```

## Features

### Stub

Factory to create object through Reflection in order to bypass the constructor.

----------------

```php
<?php

class MyEntity 
{
    /** @var string */
    private $property1;

    /** @var string */
    private $property2;

    public function __construct(string $property1, string $property2) 
    {
        $this->property1 = $property1;
        $this->property2 = $property2;
    }

    public function salute() : string 
    {
        return sprintf('Hello %s!', $this->property2);
    }
}
```

When testing method `salute()`, you only need the tested class to have `property2` set, you don't want to worry about `property1`. 
Therefore in your test you can initialize `MyEntity` using `Stub::create()` like this:

```php
$myEntity = Stub::create(MyEntity::class, ['property2' => 'world']);

self::assertSame('Hello world!', $myEntity->salute());
```

It comes handy when class constructor has more arguments and most of them are not required for your test.

It is possible to extend stubs:

```php
$myEntity = Stub::create(MyEntity::class, ['property2' => 'world']);
$myEntity = Stub::extends($myEntity, ['property1' => 'value']);

// property 1 and 2 are set now
self::assertSame('Hello world!', $myEntity->salute());
```

### Test Checks

Test Checks are used to assert that tests comply with your suite's standards (are final, extend correct TestCaseBase etc.)

To run them, e.g. create a test case like in the following example: 

```php
<?php

use Cdn77\TestUtils\TestCheck\TestCheck;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;

 #[CoversNothing]
 #[Group('integration')]
final class SuiteComplianceTest extends TestCaseBase
{
    /** @dataProvider providerChecks */
    public function testChecks(TestCheck $check) : void
    {
        $check->run($this);
    }

    /** @return Generator<string, array{callable(self): TestCheck}> */
    public static function providerChecks() : Generator
    {
        $testDir = ROOT_PROJECT_DIR . '/tests';
        $testFilePathNames = \Symfony\Component\Finder\Finder::create()
            ->in($testDir)
            ->files()
            ->name('*Test.php');

        yield 'Every test has group' => [
            new EveryTestHasGroup($testFilePathNames),
        ];
        
        ...
    }
}
```

### Every test has group

Asserts that all tests have a `#[Group('x')]` attribute 

:x:
```php
final class FooTest extends TestCase
```

:heavy_check_mark:
```php
use PHPUnit\Framework\Attributes\Group;

 #[Group('unit')]
final class FooTest extends TestCase
```

Configured in test provider as

```php
yield 'Every test has group' => [
    new EveryTestHasGroup($testFiles),
];
```

### Every test has same namespace as covered class

Asserts that all test share same namespace with class they're testing.  
Consider src namespace `Ns` and test namespace `Ns/Tests` then for test `Ns/Tests/UnitTest` must exist class `Ns/Unit`. 

You can use `#[CoversClass]` attribute to link test with tested class.  
Use `#[CoversNothing]` attribute to skip this check.

Don't forget to enable `requireCoverageMetadata="true"` in phpunit config file.

```php
namespace Ns;

final class Unit {} 
```

:x:
```php
namespace Ns\Tests;

final class NonexistentUnitTest extends TestCase {}
```

```php
namespace Ns\Tests\Sub;

final class UnitTest extends TestCase {}
```

:heavy_check_mark:
```php
namespace Ns\Tests;

final class UnitTest extends TestCase {}
```

```php
namespace Ns\Tests\Sub;

use PHPUnit\Framework\Attributes\CoversClass;

 #[CoversClass('\Ns\Unit')]
final class UnitTest extends TestCase {}
```

Configured in test provider as

```php
yield 'Every test has same namespace as tested class' => [
    new EveryTestHasSameNamespaceAsCoveredClass($testFiles),
];
```

### Every test inherits from testcase base class

Consider you have a base for all tests and want each of them extend it.

```php
abstract class TestCaseBase extends \PHPUnit\Framework\TestCase {}
```

:x:
```php
final class FooTest extends \PHPUnit\Framework\TestCase
```

:heavy_check_mark:
```php
final class FooTest extends TestCaseBase
```

Configured in test provider as

```php
yield 'Every test inherits from TestCase Base Class' => [
    new EveryTestInheritsFromTestCaseBaseClass(
        $testFiles,
        TestCaseBase::class
    ),
];
```

### Every test is final

Asserts all tests are final so they cannot be extended

:x:
```php
class FooTest extends TestCase
```

:heavy_check_mark:
```php
final class FooTest extends TestCase
```

Configured in test provider as

```php
yield 'Every test is final' => [
    new EveryTestIsFinal($testFiles),
];
```

[GA Image]: https://github.com/cdn77/TestUtils/workflows/CI/badge.svg

[GA Link]: https://github.com/cdn77/TestUtils/actions?query=workflow%3A%22CI%22+branch%3Amaster

[Coverage Image]: https://codecov.io/gh/cdn77/TestUtils/branch/master/graph/badge.svg

[CodeCov Link]: https://codecov.io/gh/cdn77/TestUtils/branch/master

[Downloads Image]: https://poser.pugx.org/cdn77/test-utils/d/total.svg

[Packagist Image]: https://poser.pugx.org/cdn77/test-utils/v/stable.svg

[Packagist Link]: https://packagist.org/packages/simpod/test-utils

[Infection Image]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fcdn77%2FTestUtils%2Fmaster

[Infection Link]: https://dashboard.stryker-mutator.io/reports/github.com/cdn77/TestUtils/master
