# blade-wants-attributes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stephancasas/blade-wants-attributes.svg?style=flat-square)](https://packagist.org/packages/stephancasas/blade-wants-attributes) [![Total Downloads](https://img.shields.io/packagist/dt/stephancasas/blade-wants-attributes.svg?style=flat-square)](https://packagist.org/packages/stephancasas/blade-wants-attributes)

`blade-wants-attributes` offers you the ability to use Blade/HTML-defined attributes within the constructors of Laravel Blade class components.

### Why?

Blade class components have many advantages, but require developers to declare attributes as constructor arguments if they wish to use them in the class signature. Because of this requirement, attributes containing non-standard characters (e.g. `wire:model`, `wire:model.defer`, etc.) are completely inaccessible to the class constructor or methods called by the class constructor.

`blade-wants-attributes` registers a Blade pre-compiler which provides attributes to the `ViewServiceProvider` just prior to initialization of a component instance. Attributes are then made accessible to your component's constructor by adding the `WantsAttributes` trait to the class signature and calling `$this->wantsAttributes()` in the constructor.

##### _Okay, but why?_

It can be useful — particularly if you're writing a components package — to extract complex component logic into class methods. For example, you may — at times — wish to dynamically/conditionally render markup for Livewire or AlpineJS. To do this, you'll need to access non-standard HTML attributes which are unavailable to Blade class components.

## Installation

You can install the package via composer:

```bash
composer require stephancasas/blade-wants-attributes
```

## Usage

Apply the `StephanCasas\BladeWantsAttributes\Traits\WantsAttributes` trait to your class component, and call `$this->wantsAttributes()`.

```php
namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    use StephanCasas\BladeWantsAttributes\Traits\WantsAttributes;

    public $wireModel;

    public function __construct()
    {
        $this->wantsAttributes();

        $this->wireModel = $this->attributes
            ->get('wire:model');
    }

    //...

    public function render()
    {
        return view('components.select');
    }
}
```

Alternatively, if you do not wish to apply the attribute bag to your component's `$attributes` property, you can also access the provided attributes via the `$this->getAllAttributes()` method:

```php
namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    use StephanCasas\BladeWantsAttributes\Traits\WantsAttributes;

    public $isLivewire;

    public function __construct()
    {
        $this->isLivewire = $this->getAllAttributes()
            ->has('wire:model');
    }

    //...

    public function render()
    {
        return view('components.select');
    }
}
```

## License

MIT — see [License File](LICENSE.md) for more information.