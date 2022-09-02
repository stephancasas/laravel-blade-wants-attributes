<?php

namespace StephanCasas\BladeWantsAttributes\Support;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\ComponentTagCompiler;

class BladeWantsAttributesTagCompiler extends ComponentTagCompiler
{
    protected string $shareKey = '__attributes';
    protected array $eligibleClasses = [];
    protected string $currentInstanceHeader;

    // the component and attributes prefix pattern in blade output
    const COMPONENT_PREFIX = "##BEGIN-COMPONENT-CLASS##@component('";
    const ATTRIBUTES_PREFIX = '<?php $component->withAttributes(';

    public function compile(string $value)
    {
        $this->shareKey = config('blade-wants-attributes.share_key');
        $this->findEligibleClasses($value);

        return array_reduce(
            $this->eligibleClasses,
            fn ($acc, $class) => $this->handleComponentClass($acc, $class),
            $value
        );
    }

    /**
     * Find attribute-wanting component classes in the given Blade output.
     * @param mixed $blade The Blade output to evaluate
     * @return void 
     */
    protected function findEligibleClasses(string $blade)
    {
        $regex = '(?<=' . preg_quote(self::COMPONENT_PREFIX) . ")[^']+";
        preg_match_all("/$regex/s", $blade, $this->eligibleClasses);

        return $this->eligibleClasses = array_filter(
            $this->eligibleClasses[0],
            fn ($class) => self::componentClassWantsAttributes($class)
        );
    }

    /**
     * Find and prepend attributes to all components of the given class.
     * @param mixed $blade The Blade output to evaluate.
     * @param mixed $class The component class to handle.
     * @return string 
     */
    protected function handleComponentClass(string $blade, string $class)
    {
        $this->currentInstanceHeader = self::COMPONENT_PREFIX . $class;
        $regex = preg_quote($this->currentInstanceHeader);

        return array_reduce(
            preg_split("/$regex/s", $blade, -1, PREG_SPLIT_OFFSET_CAPTURE),
            function ($acc, $componentInstance) {
                [$componentInstance, $i] = $componentInstance;
                if (!$i) {
                    return $componentInstance;
                }

                $withAttributes = $this->handleComponentInstance(
                    $componentInstance
                );

                return "$acc$withAttributes";
            },
            ""
        );
    }

    /**
     * Prepend the attributes helper on the component instance's Blade output.
     * @param string $instance The Blade output to evaluate.
     * @return string 
     */
    protected function handleComponentInstance(string $instance)
    {
        $attributes = [];
        $regex = preg_quote(self::ATTRIBUTES_PREFIX);
        preg_match("/$regex.*/", $instance, $attributes);

        if (!count($attributes)) return "$instance";

        $arr = (string)Str::of($attributes[0])
            ->after('(')->beforeLast(')');

        $prepend = "<?php \$__env->share('$this->shareKey', $arr); ?>";

        return "$prepend\n$this->currentInstanceHeader$instance";
    }

    /**
     * Does the given component class want attributes?
     * @param string $class The component class to evaluate
     * @return bool
     */
    protected static function componentClassWantsAttributes(string $class)
    {
        return (new ReflectionClass($class))
            ->hasMethod('wantsAttributes');
    }
}
