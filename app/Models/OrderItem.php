<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['order_id', 'product_id', 'name', 'sku', 'options', 'image', 'price', 'quantity', 'total'];

    protected $casts = [
        'options' => 'array',  // [{name, value}] as chosen by the customer
        'price' => 'float',
        'quantity' => 'integer',
        'total' => 'float',
    ];

    /**
     * Chosen options as one line, e.g. "Amperage: 16 A · Size: medium".
     */
    public function optionsText(): ?string
    {
        if (empty($this->options)) {
            return null;
        }

        return collect($this->options)->map(fn (array $option) => "{$option['name']}: {$option['value']}")->implode(' · ');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
