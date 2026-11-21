<?php

namespace App\Http\Resources;

use App\Models\ProductSpec;
use Illuminate\Support\Collection;

/**
 * Product page: card data plus gallery, specifications, "pairs with" and related items.
 * Expects specs and images to be eager loaded; pairs and related are passed in.
 *
 * @mixin \App\Models\Product
 */
class ProductDetailResource extends ProductCardResource
{
    private Collection $pairs;

    private Collection $related;

    public function withLinks(Collection $pairs, Collection $related): static
    {
        $this->pairs = $pairs;
        $this->related = $related;

        return $this;
    }

    public function toArray($request): array
    {
        return [
            ...parent::toArray($request),
            'images' => $this->images->map->url()->values(),
            'specs' => $this->specs->map(fn (ProductSpec $spec) => [
                'label' => $spec->label,
                'value' => $spec->value,
                'ltr' => $spec->isTechnical(),
            ])->values(),
            'pairs' => ProductCardResource::collection($this->pairs ?? collect())->resolve($request),
            'related' => ProductCardResource::collection($this->related ?? collect())->resolve($request),
        ];
    }
}
