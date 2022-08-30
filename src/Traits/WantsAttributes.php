<?php

namespace StephanCasas\BladeWantsAttributes\Traits;

trait WantsAttributes
{
    /**
     * Assign all HTML/Blade attributes to the component's $attribute property.
     * @return void 
     */
    protected function wantsAttributes()
    {
        $this->attributes = $this->getAllAttributes();
    }

    /**
     * Get all HTML/Blade component attributes as a new attribute bag.
     * @return \Illuminate\View\ComponentAttributeBag
     */
    protected function getAllAttributes()
    {
        $share = config('blade-wants-attributes.share_key') ?: '__attributes';
        return $this->newAttributeBag(app('view')->shared($share));
    }
}
