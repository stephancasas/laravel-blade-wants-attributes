<?php

namespace Tests\Unit\FakeComponent;

use StephanCasas\BladeWantsAttributes\Traits\WantsAttributes;

class WantingAttributes extends BaseComponent
{
    use WantsAttributes;

    public function __construct()
    {
        $this->wantsAttributes();
    }
}
