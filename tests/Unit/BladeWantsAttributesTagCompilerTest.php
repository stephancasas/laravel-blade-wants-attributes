<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use StephanCasas\BladeWantsAttributes\Support\BladeWantsAttributesTagCompiler;

class BladeWantsAttributesTagCompilerTest extends UnitTestCase
{
    /**
     * @dataProvider bladeProvider
     */
    public function test_it_should_identify_components_using_trait($blade)
    {
        $baseCompiler  = app('blade.compiler');
        $blade = $this->invokeMethod(
            $baseCompiler,
            'compileComponentTags',
            [$blade]
        );

        $compiler = app(BladeWantsAttributesTagCompiler::class);
        $classes = $this->invokeMethod(
            $compiler,
            'findEligibleClasses',
            [$blade]
        );

        $this->assertCount(2, $classes);
    }

    /**
     * @dataProvider bladeProvider
     */
    public function test_it_should_only_provide_to_components_using_trait($blade)
    {
        $renderOutput = app('blade.compiler')->render($blade);
        $needle = 'aria-description="foo"';
        $regex = preg_quote($needle);

        $this->assertSame(1, preg_match(
            "/$regex/",
            $renderOutput
        ));
    }

    /**
     * @dataProvider bladeProvider
     */
    public function test_it_should_provide_non_standard_attributes($blade)
    {
        $renderOutput = app('blade.compiler')->render($blade);

        $this->assertStringContainsString(
            'wire:model.defer="foo"',
            $renderOutput
        );
    }

    /** 
     * @dataProvider bladeProvider 
     * */
    public function test_it_should_provide_attributes_to_differently_named_components_using_trait($blade)
    {
        $renderOutput = app('blade.compiler')->render($blade);

        $this->assertStringContainsString(
            'wire:model.defer="foo"',
            $renderOutput
        );

        $this->assertStringContainsString(
            'wire:model.defer="bar"',
            $renderOutput
        );
    }

    public function test_it_should_not_care_about_component_length_or_order()
    {
        $blade = <<<blade
        <x-wanting-attributes-different-name wire:model="bar" description="foo" />
        <x-wanting-attributes wire:model="foo" description="foo" />
        blade;

        $renderOutput = app('blade.compiler')->render($blade);

        $this->assertStringContainsString(
            'wire:model.defer="foo"',
            $renderOutput
        );

        $this->assertStringContainsString(
            'wire:model.defer="bar"',
            $renderOutput
        );
    }

    public function bladeProvider(): array
    {
        $blade = <<<blade
        <x-wanting-attributes wire:model="foo" description="foo" />
        <x-wanting-attributes-different-name wire:model="bar" description="foo" />
        <x-without-attributes wire:model="foo" description="foo" />
        blade;

        return [[$blade]];
    }
}
