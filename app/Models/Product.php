<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations, Publishable;

    protected $fillable = [
        'slug', 'sku', 'category_id', 'brand_id', 'subcategory', 'series', 'name', 'short_description', 'unit',
        'price', 'tax_rate', 'stock', 'range_min', 'range_max', 'sort_order', 'is_active',
    ];

    public array $translatable = ['name', 'short_description', 'subcategory', 'unit'];

    protected $casts = [
        'price' => 'float',
        'tax_rate' => 'float',
        'stock' => 'integer',     // NULL = stock not tracked
        'range_min' => 'float',
        'range_max' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            $product->search_text = self::searchText([
                $product->sku,
                $product->slug,
                $product->series,
                $product->brand?->name,
                ...array_values($product->getTranslations('name')),
                ...array_values($product->getTranslations('short_description')),
            ]);
        });
    }

    /**
     * Normalised, lower-cased text the storefront search matches against.
     */
    public static function searchText(array $parts): string
    {
        return mb_strtolower(implode(' ', array_unique(array_filter(array_map('trim', $parts)))));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProductSpec::class)->orderBy('sort_order')->orderBy('id');
    }

    public function pairs(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_pairs', 'product_id', 'paired_product_id');
    }

    /**
     * Products that can be shown and ordered on the storefront: visible, priced,
     * in a visible category.
     */
    public function scopeSellable(Builder $query): Builder
    {
        return $query->active()
            ->where('price', '>', 0)
            ->whereHas('category', fn (Builder $category) => $category->active());
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        foreach (preg_split('/\s+/u', mb_strtolower(trim($term))) ?: [] as $word) {
            if ($word !== '') {
                $query->where('search_text', 'like', '%'.addcslashes($word, '%_\\').'%');
            }
        }

        return $query;
    }

    public function tracksStock(): bool
    {
        return $this->stock !== null;
    }

    public function mainImageUrl(): ?string
    {
        return media_url($this->images->first()?->path);
    }
}
