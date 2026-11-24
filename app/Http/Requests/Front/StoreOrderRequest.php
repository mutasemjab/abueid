<?php

namespace App\Http\Requests\Front;

use App\Enums\PaymentMethod;
use App\Http\Requests\Front\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreOrderRequest extends FormRequest
{
    use NormalizesPhone;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * A guest may open an account while ordering (create_account + password).
     */
    public function wantsAccount(): bool
    {
        return ! $this->user('customer') && $this->boolean('create_account');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => [...$this->phoneRules(), Rule::when($this->wantsAccount(), [Rule::unique('customers', 'phone')])],
            'address' => ['required', 'string', 'min:4', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'area_id' => ['required', 'integer', Rule::exists('delivery_areas', 'id')->where('is_active', true)],
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id' => ['required', 'string', 'max:100'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.options' => ['nullable', 'array', 'max:20'],    // {option id: value id}
            'items.*.options.*' => ['integer'],
            'create_account' => ['nullable', 'boolean'],
            'password' => [Rule::requiredIf($this->wantsAccount()), 'nullable', 'string', 'min:6', 'max:100'],
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
            'address' => __('front.f_address'),
            'notes' => __('front.f_notes'),
            'area_id' => __('front.co_address'),
            'payment_method' => __('front.co_pay'),
            'items' => __('front.cart'),
            'password' => __('front.f_password'),
        ];
    }
}
