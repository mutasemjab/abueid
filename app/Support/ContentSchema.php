<?php

namespace App\Support;

use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Validation and persistence of schema-driven content
 * (see config/sections.php and config/settings.php).
 *
 * Form inputs are named {prefix}[field] or {prefix}[field][locale];
 * image fields can be cleared with remove[field].
 */
final class ContentSchema
{
    public const IMAGE_RULES = ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:4096'];

    public function __construct(
        private readonly array $fields,
        private readonly string $prefix = 'content',
    ) {
    }

    public function rules(): array
    {
        $rules = [];

        foreach ($this->fields as $name => $field) {
            if ($this->isImage($field)) {
                $rules["{$this->prefix}.{$name}"] = self::IMAGE_RULES;
                $rules["remove.{$name}"] = ['nullable', 'boolean'];

                continue;
            }

            $fieldRules = [($field['required'] ?? false) ? 'required' : 'nullable', ...$this->typeRules($field)];

            if ($this->isTranslatable($field)) {
                foreach (locales() as $locale) {
                    $rules["{$this->prefix}.{$name}.{$locale}"] = $fieldRules;
                }
            } else {
                $rules["{$this->prefix}.{$name}"] = $fieldRules;
            }
        }

        return $rules;
    }

    /**
     * Human readable attribute names for validation messages.
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->fields as $name => $field) {
            $label = self::label($name);

            if ($this->isTranslatable($field)) {
                foreach (locales() as $locale) {
                    $attributes["{$this->prefix}.{$name}.{$locale}"] = "{$label} ({$locale})";
                }
            } else {
                $attributes["{$this->prefix}.{$name}"] = $label;
            }
        }

        return $attributes;
    }

    /**
     * Merge the (already validated) request input into the current content,
     * storing uploaded images and removing replaced ones.
     */
    public function apply(array $current, Request $request, MediaService $media, string $folder): array
    {
        foreach ($this->fields as $name => $field) {
            $key = "{$this->prefix}.{$name}";

            if ($this->isImage($field)) {
                $current[$name] = $this->applyImage($current[$name] ?? null, $request->file($key), $request->boolean("remove.{$name}"), $media, $folder);

                continue;
            }

            $value = $request->input($key);

            $current[$name] = match (true) {
                $this->isTranslatable($field) => collect(locales())->mapWithKeys(fn ($locale) => [$locale => $value[$locale] ?? null])->all(),
                $field['type'] === 'number' => is_numeric($value) ? $value + 0 : null,
                default => $value,
            };
        }

        return $current;
    }

    /**
     * Delete every image referenced by the given content.
     */
    public function deleteImages(array $content, MediaService $media): void
    {
        foreach ($this->fields as $name => $field) {
            if ($this->isImage($field)) {
                $media->delete($content[$name] ?? null);
            }
        }
    }

    public static function label(string $field): string
    {
        return __("sections.fields.{$field}");
    }

    private function applyImage(?string $currentPath, ?UploadedFile $upload, bool $remove, MediaService $media, string $folder): ?string
    {
        if ($upload) {
            return $media->replace($currentPath, $upload, $folder);
        }

        if ($remove) {
            $media->delete($currentPath);

            return null;
        }

        return $currentPath;
    }

    private function typeRules(array $field): array
    {
        $rules = match ($field['type']) {
            'textarea' => ['string', 'max:5000'],
            'number' => ['numeric'],
            'url' => ['url', 'max:500'],
            default => ['string', 'max:255'],
        };

        return [...$rules, ...($field['rules'] ?? [])];
    }

    private function isImage(array $field): bool
    {
        return $field['type'] === 'image';
    }

    private function isTranslatable(array $field): bool
    {
        return (bool) ($field['translatable'] ?? false);
    }
}
