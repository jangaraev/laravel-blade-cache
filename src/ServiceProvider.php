<?php

namespace Jangaraev\LaravelBladeCache;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    public function boot(): void
    {
        Blade::directive('cache', function ($args): string {
            return "<?php echo \\Jangaraev\LaravelBladeCache\\BladeCache::get(\"{$args}\") ?>";
        });
    }
}
