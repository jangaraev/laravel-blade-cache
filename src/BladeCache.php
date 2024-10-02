<?php

namespace Jangaraev\LaravelBladeCache;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BladeCache
{
    public static function get(string $args): string
    {
        [$view, $ttl] = self::extractArgumentsFromDirective($args);

        return Cache::remember(self::getQualifiedCacheKey($view), $ttl * 60, function () use ($view) {
            return self::render($view);
        });
    }

    public function reset(string $key): void
    {
        $locales = [config('app.locale')];

        if (class_exists('LaravelLocalization')) {
            $locales = array_keys(LaravelLocalization::getSupportedLocales());
        }

        foreach ($locales as $locale) {
            if (Cache::has($cacheKey = self::getQualifiedCacheKey($key, $locale))) {
                Cache::forget($cacheKey);
            }
        }
    }


    /**
     * @return array<string, int>
     */
    private static function extractArgumentsFromDirective(string $args): array
    {
        $explodedArgs = explode(',', $args);

        if (count($explodedArgs) < 1) {
            throw new \InvalidArgumentException();
        }

        $view = trim($explodedArgs[0], "'\"");
        $ttl = isset($explodedArgs[1]) ? trim($explodedArgs[1]) : 60;

        if (!is_numeric($ttl)) {
            throw new \InvalidArgumentException('TTL argument should be an integer.');
        }

        return [$view, (int)$ttl];
    }

    protected static function render(string $view): string
    {
        return (string)View::make($view);
    }

    protected static function getQualifiedCacheKey(string $view, string $locale = null): string
    {
        $view = str_replace('.', '_', $view);

        if (is_null($locale)) {
            $locale = class_exists('LaravelLocalization')
                ? LaravelLocalization::getCurrentLocale()
                : App::getLocale();
        }

        return "blade-cache.{$view}.{$locale}";
    }
}
