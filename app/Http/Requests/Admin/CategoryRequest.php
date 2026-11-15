<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class CategoryRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('categories', 'slug')->ignore($this->route('category'))],
            ...$this->translatable('name', ['required', 'string', 'max:150']),
            ...$this->translatable('blurb', ['nullable', 'string', 'max:255']),
            ...$this->translatable('subcategories', ['nullable', 'string', 'max:2000']),
            'image' => $this->imageRules(),
            'remove_image' => ['nullable', 'boolean'],
            ...$this->publishingRules(),
        ];
    }

    public function attributes(): array
    {
        return $this->translatableAttributes([
            'name' => __('admin.fields.name'),
            'blurb' => __('admin.fields.blurb'),
            'subcategories' => __('admin.fields.subcategories'),
        ]) + [
            'slug' => __('admin.fields.slug'),
            'image' => __('admin.fields.image'),
        ];
    }
}
