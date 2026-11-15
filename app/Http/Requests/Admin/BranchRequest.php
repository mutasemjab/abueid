<?php

namespace App\Http\Requests\Admin;

class BranchRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            ...$this->translatable('name', ['required', 'string', 'max:150']),
            ...$this->translatable('city', ['required', 'string', 'max:100']),
            ...$this->translatable('address', ['required', 'string', 'max:255']),
            ...$this->translatable('hours', ['nullable', 'string', 'max:150']),
            'phone' => ['nullable', 'string', 'max:30'],
            'map_url' => ['nullable', 'url', 'max:500'],
            'photo' => $this->imageRules(),
            'remove_photo' => ['nullable', 'boolean'],
            'is_main' => ['required', 'boolean'],
            ...$this->publishingRules(),
        ];
    }

    public function attributes(): array
    {
        return $this->translatableAttributes([
            'name' => __('admin.fields.name'),
            'city' => __('admin.fields.city'),
            'address' => __('admin.fields.address'),
            'hours' => __('admin.fields.hours'),
        ]) + [
            'phone' => __('admin.fields.phone'),
            'map_url' => __('admin.fields.map_url'),
            'photo' => __('admin.fields.photo'),
        ];
    }
}
