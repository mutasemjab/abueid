<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class BrandRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('brands', 'slug')->ignore($this->route('brand'))],
            'name' => ['required', 'string', 'max:150'],
            ...$this->translatable('origin', ['nullable', 'string', 'max:100']),
            ...$this->translatable('product_line', ['nullable', 'string', 'max:255']),
            'logo' => $this->imageRules(),
            'remove_logo' => ['nullable', 'boolean'],
            ...$this->publishingRules(),
        ];
    }

    public function attributes(): array
    {
        return $this->translatableAttributes([
            'origin' => __('admin.fields.origin'),
            'product_line' => __('admin.fields.product_line'),
        ]) + [
            'slug' => __('admin.fields.slug'),
            'name' => __('admin.fields.name'),
            'logo' => __('admin.fields.logo'),
        ];
    }
}
