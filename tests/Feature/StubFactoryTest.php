<?php

declare(strict_types=1);

namespace Cdn77\TestUtils\Tests\Feature;

use Cdn77\TestUtils\Tests\BaseTestCase;
use ReflectionException;
use TypeError;

final class StubFactoryTest extends BaseTestCase
{
    public function testValueIsDefaultWhenNotSet() : void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('null returned');

        $stub = $this->makeStub(SimpleClass::class);

        $stub->getProperty1();

        self::assertSame('default value', $stub->getPropertyWithDefaultValue());
    }

    public function testPropertyIsSetBypassingConstructor() : void
    {
        $stub = $this->makeStub(SimpleClass::class, ['property1' => 'value']);

        self::assertSame('value', $stub->getProperty1());
    }

    public function testParentPropertyIsSetBypassingConstructor() : void
    {
        $stub = $this->makeStub(SimpleClass::class, ['parentProperty' => 'value']);

        self::assertSame('value', $stub->getParentProperty());
    }

    public function testSettingNonexistentPropertyThrowsException() : void
    {
        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage('Property "nonexistentProperty" not found');

        $this->makeStub(SimpleClass::class, ['nonexistentProperty' => 'value']);
    }
}
