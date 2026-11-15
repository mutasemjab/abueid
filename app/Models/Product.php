<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations, Publishable;

    protected $fillable = [
        'slug', 'sku', 'category_id', 'brand_id', 'subcategory', 'series', 'name', 'short_description',
        'price', 'stock', 'range_min', 'range_max', 'sort_order', 'is_active',
    ];

    public array $translatable = ['name', 'short_description', 'subcategory'];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'range_min' => 'float',
        'range_max' => 'float',
    ];

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

    public function mainImageUrl(): ?string
    {
        return media_url($this->images->first()?->path);
    }
}
