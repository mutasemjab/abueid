<?php

namespace App\Http\Resources;

use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Product as shown on cards, in the cart and in search results.
 * Expects category, brand, images and options.values to be eager loaded.
 *
 * @mixin \App\Models\Product
 */
class ProductCardResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id' => $this->slug,
            'sku' => $this->sku,
            'cat' => $this->category?->slug,
            'sub' => $this->subcategory ?: null,
            'brand' => $this->brand?->is_active ? $this->brand->slug : null,
            'brandName' => $this->brand?->is_active ? $this->brand->name : null,
            'series' => $this->series,
            'name' => $this->name,
            'short' => $this->short_description ?: null,
            'price' => $this->price,
            'unit' => $this->unit ?: null,
            'stock' => $this->stock,   // null = not tracked
            'img' => media_url($this->images->first()?->path),
            // Choices the customer must make before adding to the cart (empty for most products).
            'options' => $this->options->map(fn (ProductOption $option) => [
                'id' => $option->id,
                'name' => $option->name,
                'values' => $option->values->map(fn (ProductOptionValue $value) => [
                    'id' => $value->id,
                    'label' => $value->label,
                    'delta' => $value->price_delta,
                ])->values(),
            ])->values(),
        ];
    }
}
