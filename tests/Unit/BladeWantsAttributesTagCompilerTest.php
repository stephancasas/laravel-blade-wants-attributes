<?php

namespace Tests\Unit;

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

        $this->assertCount(1, $classes);
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

    public function bladeProvider(): array
    {
        $blade = <<<blade
        <x-wanting-attributes wire:model="foo" description="foo" />
        <x-without-attributes wire:model="foo" description="foo" />
        blade;

        return [[$blade]];
    }
}
