<?php

namespace Jangaraev\LaravelBladeCache;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BladeCache
{
    const VIEW = 'view';
    const TTL = 'ttl';
    const VARIABLES = 'variables';


    public function reset(string $key): void
    {
        $locales = [config('app.locale')];

        if (class_exists('LaravelLocalization')) {
            $locales = array_keys(LaravelLocalization::getSupportedLocales());
        }

        foreach ($locales as $locale) {
            $cacheKey = self::getQualifiedCacheKey($key, $locale);

            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
            }
        }
    }

    public static function get(string $key): ?string
    {
        return Cache::remember(self::getQualifiedCacheKey($key), self::getSection($key, self::TTL), function () use ($key) {
            return self::render($key);
        });
    }

    protected static function getSection(string $key, string $param = null): mixed
    {
        if (is_null($param)) {
            return config('blade-cache.' . $key);
        }

        $value = config('blade-cache.' . $key)[$param] ?? null;

        if (self::TTL === $param) {
            return ($value ?? 60) * 60;
        }

        return $value;
    }

    protected static function render(string $key): ?string
    {
        $section = self::getSection($key);

        if (!$section) {
            return null;
        }

        return (string)view($section[self::VIEW], call_user_func($section[self::VARIABLES]));
    }

    protected static function getQualifiedCacheKey(string $key, string $locale = null): string
    {
        if (is_null($locale)) {
            $locale = App::getLocale();
        }

        return "blade-cache.{$key}.{$locale}";
    }
}
