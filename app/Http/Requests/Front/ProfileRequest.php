<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:customer'],
            'password' => ['nullable', 'string', 'min:6', 'max:100', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('front.f_name'),
            'current_password' => __('front.f_current_password'),
            'password' => __('front.f_new_password'),
        ];
    }
}
