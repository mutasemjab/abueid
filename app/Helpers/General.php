<?php

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if (! function_exists('locales')) {
    /**
     * Supported content locales, e.g. ['ar', 'en'].
     *
     * @return list<string>
     */
    function locales(): array
    {
        return LaravelLocalization::getSupportedLanguagesKeys();
    }
}

if (! function_exists('localized')) {
    /**
     * Resolve a ["ar" => …, "en" => …] value to the given (or current) locale.
     * Non-array values are returned unchanged.
     */
    function localized(mixed $value, ?string $locale = null): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        return $value[$locale ?? app()->getLocale()] ?? $value[config('app.fallback_locale')] ?? null;
    }
}

if (! function_exists('media_url')) {
    /**
     * Public URL of a stored media path (relative to the web root) or null.
     */
    function media_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return preg_match('#^https?://#i', $path) ? $path : asset($path);
    }
}

if (! function_exists('input_name')) {
    /**
     * Convert a dot path ("content.title.ar") to an HTML input name ("content[title][ar]").
     */
    function input_name(string $path): string
    {
        return preg_replace('/\.([^.]+)/', '[$1]', $path);
    }
}

if (! function_exists('is_rtl')) {
    function is_rtl(?string $locale = null): bool
    {
        return LaravelLocalization::getSupportedLocales()[$locale ?? app()->getLocale()]['script'] === 'Arab';
    }
}
