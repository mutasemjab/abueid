<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'area_id' => ['required', 'integer', Rule::exists('delivery_areas', 'id')->where('is_active', true)],
            'address' => ['required', 'string', 'min:4', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'area_id' => __('front.co_address'),
            'address' => __('front.f_address'),
        ];
    }
}
