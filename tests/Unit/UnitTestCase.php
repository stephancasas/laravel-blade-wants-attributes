<?php

namespace Tests\Unit;

use ReflectionClass;
use Orchestra\Testbench\TestCase;
use StephanCasas\BladeWantsAttributes\BladeWantsAttributesServiceProvider;

class UnitTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        static::registerFakeComponents();
    }

    protected static function registerFakeComponents()
    {
        app('blade.compiler')->component(
            FakeComponent\WantingAttributes::class,
            'wanting-attributes'
        );
        app('blade.compiler')->component(
            FakeComponent\WithoutAttributes::class,
            'without-attributes'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            BladeWantsAttributesServiceProvider::class
        ];
    }

    /** Call protected/private method of a class */
    public function invokeMethod(mixed $object, string $method, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
