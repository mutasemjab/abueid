<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class ProductRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        // Drop spec rows that were added in the form but left completely empty.
        $specs = collect($this->input('specs', []))
            ->filter(fn ($spec) => filled(array_filter([...(array) ($spec['label'] ?? []), ...(array) ($spec['value'] ?? [])])))
            ->values()
            ->all();

        $this->merge(['specs' => $specs]);
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('products', 'slug')->ignore($product)],
            'sku' => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'series' => ['nullable', 'string', 'max:100'],
            ...$this->translatable('name', ['required', 'string', 'max:255']),
            ...$this->translatable('short_description', ['nullable', 'string', 'max:255']),
            ...$this->translatable('subcategory', ['nullable', 'string', 'max:100']),
            ...$this->translatable('unit', ['nullable', 'string', 'max:30']),
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'range_min' => ['nullable', 'numeric', 'min:0', 'max:999999', 'required_with:range_max'],
            'range_max' => ['nullable', 'numeric', 'max:999999', 'required_with:range_min', 'gte:range_min'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => $this->imageRules(),
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'specs' => ['array', 'max:30'],
            ...$this->translatable('specs.*.label', ['required', 'string', 'max:100']),
            ...$this->translatable('specs.*.value', ['required', 'string', 'max:255']),
            'pairs' => ['nullable', 'array'],
            'pairs.*' => ['integer', 'distinct', 'exists:products,id', Rule::notIn(array_filter([$product?->id]))],
            ...$this->publishingRules(),
        ];
    }

    public function attributes(): array
    {
        return $this->translatableAttributes([
            'name' => __('admin.fields.name'),
            'short_description' => __('admin.fields.short_description'),
            'subcategory' => __('admin.fields.subcategory'),
            'unit' => __('admin.fields.unit'),
            'specs.*.label' => __('admin.fields.spec_label'),
            'specs.*.value' => __('admin.fields.spec_value'),
        ]) + [
            'slug' => __('admin.fields.slug'),
            'sku' => __('admin.fields.sku'),
            'category_id' => __('admin.fields.category'),
            'brand_id' => __('admin.fields.brand'),
            'series' => __('admin.fields.series'),
            'price' => __('admin.fields.price'),
            'tax_rate' => __('admin.fields.tax_rate'),
            'stock' => __('admin.fields.stock'),
            'range_min' => __('admin.fields.range_min'),
            'range_max' => __('admin.fields.range_max'),
            'images.*' => __('admin.fields.images'),
            'pairs.*' => __('admin.fields.pairs'),
        ];
    }
}
