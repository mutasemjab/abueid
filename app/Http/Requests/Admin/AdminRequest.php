<?php

namespace App\Http\Requests\Admin;

use App\Support\ContentSchema;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base request for dashboard forms: authorisation is handled by the controller
 * permission middleware; this class adds helpers for translatable inputs.
 */
abstract class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules for a translatable input named {field}[{locale}].
     */
    protected function translatable(string $field, array $rules): array
    {
        $result = [$field => ['array']];

        foreach (locales() as $locale) {
            $result["{$field}.{$locale}"] = $rules;
        }

        return $result;
    }

    /**
     * @param  array<string, string>  $labels  field => label
     */
    protected function translatableAttributes(array $labels): array
    {
        $attributes = [];

        foreach ($labels as $field => $label) {
            $attributes[$field] = $label;

            foreach (locales() as $locale) {
                $attributes["{$field}.{$locale}"] = "{$label} ({$locale})";
            }
        }

        return $attributes;
    }

    protected function imageRules(): array
    {
        return ContentSchema::IMAGE_RULES;
    }

    protected function publishingRules(): array
    {
        return [
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
