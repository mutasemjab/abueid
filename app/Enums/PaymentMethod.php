<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CashOnDelivery = 'cod';
    case Cliq = 'cliq';

    public function label(): string
    {
        return __("front.payment_methods.{$this->value}");
    }
}
