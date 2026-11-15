<?php

namespace App\Http\Resources;

use App\Models\OrderItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class OrderResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'no' => $this->number,
            'date' => $this->created_at->format('Y-m-d'),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'name' => $this->customer_name,
            'phone' => $this->customer_phone,
            'address' => $this->address,
            'notes' => $this->notes,
            'area' => $this->area_name,
            'pay' => $this->payment_method->value,
            'sub' => $this->subtotal,
            'fee' => $this->delivery_fee,
            'total' => $this->total,
            'items' => $this->items->map(fn (OrderItem $item) => [
                'name' => $item->name,
                'sku' => $item->sku,
                'image' => media_url($item->image),
                'price' => $item->price,
                'qty' => $item->quantity,
                'total' => $item->total,
            ])->values(),
        ];
    }
}
