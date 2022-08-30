<?php

namespace StephanCasas\BladeWantsAttributes;

use Illuminate\Support\ServiceProvider;

class BladeWantsAttributesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // register the precompiler
        if (method_exists($this->app['blade.compiler'], 'precompiler')) {
            $this->app['blade.compiler']->precompiler(function ($string) {
                return app(Support\BladeWantsAttributesTagCompiler::class)
                    ->compile($string);
            });
        }

        // publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('blade-wants-attributes.php'),
            ], 'blade-wants-attributes-config');
        }
    }

    public function register()
    {
        // apply config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'blade-wants-attributes'
        );
    }
}
