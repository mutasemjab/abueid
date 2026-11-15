<?php

namespace App\Http\Requests\Front;

use App\Http\Requests\Front\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use NormalizesPhone;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => $this->phoneRules(),
            'password' => ['required', 'string', 'max:100'],
            'remember' => ['nullable', 'boolean'],
            'favourites' => ['nullable', 'array', 'max:500'],
            'favourites.*' => ['string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => __('front.f_phone'),
            'password' => __('front.f_password'),
        ];
    }
}
