<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use Illuminate\Validation\Rules\Enum;

class OrderStatusRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(OrderStatus::class)],
        ];
    }

    public function attributes(): array
    {
        return ['status' => __('admin.fields.status')];
    }
}
