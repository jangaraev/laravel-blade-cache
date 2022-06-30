<?php

namespace Jangaraev\LaravelBladeCache;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('blade-cache.php'),
        ], 'config');

        Blade::directive('cache', function ($key): string {
            return "<?php echo \\Jangaraev\LaravelBladeCache\\BladeCache::get($key) ?>";
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php', 'blade-cache'
        );
    }
}
