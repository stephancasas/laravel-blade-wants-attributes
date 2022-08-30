<?php

namespace Tests\Unit\FakeComponent;

use Illuminate\Support\Arr;
use Illuminate\View\Component;

class BaseComponent extends Component
{
    public function render()
    {
        $wireModel = Arr::get($this->attributes, 'wire:model');
        $description = Arr::get($this->attributes, 'description');

        return <<<HTML
        <input wire:model.defer="$wireModel" aria-description="$description"/>
        HTML;
    }
}
