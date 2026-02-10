<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests;

use Cdn77\TestUtils\Stub;
use Cdn77\TestUtils\Tests\Fixture\ClassWithReadonlyProperty;
use Error;
use ReflectionException;

use const PHP_VERSION_ID;

final class StubTest extends BaseTestCase
{
    public function testValueIsDefaultWhenNotSet(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('must not be accessed before initialization');

        $stub = Stub::create(SimpleClass::class);

        $x = $stub->getProperty1();
    }

    public function testPropertyIsSetBypassingConstructor(): void
    {
        $stub = Stub::create(SimpleClass::class, ['property1' => 'value']);

        self::assertSame('value', $stub->getProperty1());
    }

    public function testParentPropertyIsSetBypassingConstructor(): void
    {
        $stub = Stub::create(SimpleClass::class, ['parentProperty' => 'value']);

        self::assertSame('value', $stub->getParentProperty());
    }

    public function testSettingNonexistentPropertyThrowsException(): void
    {
        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage('Property "nonexistentProperty" not found');

        Stub::create(SimpleClass::class, ['nonexistentProperty' => 'value']);
    }

    public function testExtend(): void
    {
        $stub = Stub::create(SimpleClass::class, ['property1' => 'value']);

        $stub = Stub::extend($stub, ['property2' => 'value2']);

        self::assertSame('value', $stub->getProperty1());
        self::assertSame('value2', $stub->getProperty2());
    }

    public function testReadonlyPropertyIsSetBypassingConstructor(): void
    {
        $stub = Stub::create(ClassWithReadonlyProperty::class, ['readonlyProperty' => 'value']);

        self::assertSame('value', $stub->readonlyProperty());
    }

    public function testPrivateReadonlyPropertyIsSetBypassingConstructor(): void
    {
        $stub = Stub::create(ClassWithReadonlyProperty::class, ['parentReadonlyProperty' => 'value']);

        self::assertSame('value', $stub->parentReadonlyProperty());
    }

    /** PHP < 8.4 does not allow setting public readonly properties from different scopes */
    public function testPublicReadonlyPropertyCannotBeSet(): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $stub = Stub::create(ClassWithReadonlyProperty::class, ['parentPublicReadonlyProperty' => 'value']);
            self::assertSame('value', $stub->parentPublicReadonlyProperty);

            return;
        }

        try {
            Stub::create(ClassWithReadonlyProperty::class, ['parentPublicReadonlyProperty' => 'value']);
        } catch (Error $e) {
            self::assertStringContainsString('Cannot initialize readonly property', $e->getMessage());
            self::assertStringContainsString('from scope', $e->getMessage());

            return;
        }

        self::fail('Expected error was not thrown');
    }
}
