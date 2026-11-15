<?php

namespace App\Http\Requests\Admin;

class DeliveryAreaRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            ...$this->translatable('name', ['required', 'string', 'max:100']),
            'fee' => ['required', 'numeric', 'min:0', 'max:999'],
            ...$this->publishingRules(),
        ];
    }

    public function attributes(): array
    {
        return $this->translatableAttributes(['name' => __('admin.fields.name')]) + [
            'fee' => __('admin.fields.fee'),
        ];
    }
}
