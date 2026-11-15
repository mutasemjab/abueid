<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $fillable = ['customer_id', 'delivery_area_id', 'address', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryArea(): BelongsTo
    {
        return $this->belongsTo(DeliveryArea::class);
    }
}
