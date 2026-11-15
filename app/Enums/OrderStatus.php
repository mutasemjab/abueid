<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __("admin.orders.statuses.{$this->value}");
    }

    /** CSS modifier of the admin "pill" badge. */
    public function pill(): string
    {
        return match ($this) {
            self::New => 'pill-warning',
            self::Confirmed => 'pill-info',
            self::Delivered => 'pill-success',
            self::Cancelled => 'pill-danger',
        };
    }
}
