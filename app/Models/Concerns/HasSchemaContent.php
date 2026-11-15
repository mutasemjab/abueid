<?php

namespace App\Models\Concerns;

/**
 * Models whose fields are described by a schema (config/sections.php) and stored
 * in a single JSON "content" column. Translatable fields are stored as
 * ["ar" => "...", "en" => "..."]; plain fields are stored as scalars.
 */
trait HasSchemaContent
{
    public function initializeHasSchemaContent(): void
    {
        $this->mergeCasts(['content' => 'array']);
    }

    /**
     * Value of a field in the given (or current) locale, falling back to the app fallback locale.
     */
    public function value(string $field, ?string $locale = null): mixed
    {
        return localized($this->content[$field] ?? null, $locale);
    }

    public function imageUrl(string $field): ?string
    {
        return media_url($this->value($field));
    }
}
