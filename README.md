# Cdn77 TestUtils

[![Build Status](https://github.com/cdn77/TestUtils/workflows/CI/badge.svg?branch=master)](https://github.com/cdn77/TestUtils/actions)
[![Coverage Status](https://coveralls.io/repos/github/cdn77/TestUtils/badge.svg?branch=master)](https://coveralls.io/github/cdn77/TestUtils?branch=master)
[![Downloads](https://poser.pugx.org/cdn77/test-utils/d/total.svg)](https://packagist.org/packages/cdn77/test-utils)

## Contents

- [Installation](#installation)
- [Features](#features)
  - [Stub](#stub)
  - [AdvancedAssert](#advanced-assert)

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

### AdvancedAssert

TBD
