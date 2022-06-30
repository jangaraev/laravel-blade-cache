<?php

namespace Jangaraev\LaravelBladeCache;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BladeCache
{
    const TTL = 'ttl';
    const VARIABLES = 'variables';


    public function reset(string $view): void
    {
        $locales = [config('app.locale')];

        if (class_exists('LaravelLocalization')) {
            $locales = array_keys(LaravelLocalization::getSupportedLocales());
        }

        foreach ($locales as $locale) {
            $cacheKey = self::getQualifiedCacheKey($view, $locale);

            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
            }
        }
    }

    public static function get(string $view): bool
    {
        return Cache::remember(self::getQualifiedCacheKey($view), self::getSection($view, self::TTL), function () use ($view) {
            return self::render($view);
        });
    }

    protected static function getSection(string $view, string $param = null): mixed
    {
        if (is_null($param)) {
            return config('blade-cache.' . $view);
        }

        $value = config('blade-cache.' . $view)[$param] ?? null;

        if (self::TTL === $param) {
            return ($value ?? 60) * 60;
        }

        return $value;
    }

    protected static function render(string $view): ?string
    {
        $section = self::getSection($view);

        if (!$section) {
            return null;
        }

        return (string)view($view, self::resolveViewVariables($section[self::VARIABLES]));
    }

    protected static function getQualifiedCacheKey(string $view, string $locale = null): string
    {
        if (is_null($locale)) {
            $locale = App::getLocale();
        }

        return "blade-cache.{$view}.{$locale}";
    }

    protected static function resolveViewVariables(array $variables): array
    {
        return collect($variables)
            ->transform(fn ($cb) => call_user_func($cb));
    }
}
