<?php

namespace App\Http\Requests\Front;

use App\Http\Requests\Front\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    use NormalizesPhone;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => [...$this->phoneRules(), Rule::unique('customers', 'phone')],
            'password' => ['required', 'string', 'min:6', 'max:100', 'confirmed'],
            'favourites' => ['nullable', 'array', 'max:500'],
            'favourites.*' => ['string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return ['phone.unique' => __('front.acc_phone_taken')];
    }

    public function attributes(): array
    {
        return [
            'name' => __('front.f_name'),
            'phone' => __('front.f_phone'),
            'password' => __('front.f_password'),
        ];
    }
}
