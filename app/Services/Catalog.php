<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Storefront catalogue queries (only sellable products: visible, priced, visible category).
 */
class Catalog
{
    /**
     * Base query for product cards, with the relations cards need.
     */
    public function cards(): Builder
    {
        return Product::query()
            ->sellable()
            ->with(['category:id,slug', 'brand:id,slug,name,is_active', 'images', 'options.values']);
    }

    /**
     * "Most popular" order: products with photos first, then the dashboard order.
     */
    public function popularFirst(Builder $query): Builder
    {
        return $query->withCount('images')
            ->orderByDesc('images_count')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Products for the home page hero and "in stock" grid.
     *
     * @return Collection<int, Product>
     */
    public function featured(int $limit = 12): Collection
    {
        return $this->popularFirst($this->cards())
            ->where(fn (Builder $query) => $query->whereNull('stock')->orWhere('stock', '>', 0))
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    public function pairsOf(Product $product): Collection
    {
        return $this->cards()
            ->whereIn('id', $product->pairs()->pluck('products.id'))
            ->get();
    }

    /**
     * Same-category products (photographed ones first), excluding the given product and its pairs.
     *
     * @return Collection<int, Product>
     */
    public function relatedTo(Product $product, Collection $exclude, int $limit = 4): Collection
    {
        return $this->popularFirst($this->cards())
            ->where('category_id', $product->category_id)
            ->whereKeyNot([$product->id, ...$exclude->modelKeys()])
            ->limit($limit)
            ->get();
    }
}
