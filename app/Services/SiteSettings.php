<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Read/write access to the key-value site settings (config/settings.php).
 * Values are cached until the next save.
 */
class SiteSettings
{
    private const CACHE_KEY = 'site_settings';

    private ?array $values = null;

    /**
     * Setting value resolved to the current locale (for translatable settings).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return localized($this->raw($key)) ?? $default;
    }

    /**
     * Stored value as-is (translatable settings are ["ar" => …, "en" => …]).
     */
    public function raw(string $key): mixed
    {
        return $this->all()[$key] ?? null;
    }

    public function imageUrl(string $key): ?string
    {
        return media_url($this->raw($key));
    }

    public function all(): array
    {
        return $this->values ??= Cache::rememberForever(
            self::CACHE_KEY,
            fn () => Setting::query()->pluck('value', 'key')->all()
        );
    }

    public function save(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->values = null;
    }
}
