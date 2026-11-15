<?php

namespace App\Http\Requests\Front;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreOrderRequest extends FormRequest
{
    public const JORDAN_MOBILE = '/^07[789]\d{7}$/';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise the phone number to the local 07XXXXXXXX format.
     */
    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/[\s-]/', '', (string) $this->input('phone'));

        $this->merge(['phone' => preg_replace('/^\+?962/', '0', $phone)]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string', 'regex:'.self::JORDAN_MOBILE],
            'address' => ['required', 'string', 'min:4', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'area_id' => ['required', 'integer', Rule::exists('delivery_areas', 'id')->where('is_active', true)],
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id' => ['required', 'string', 'max:100'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],
        ];
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
        ];
    }
}
