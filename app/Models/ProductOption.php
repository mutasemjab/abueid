<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * A choice the customer makes for a product, e.g. "Amperage" or "Size".
 */
class ProductOption extends Model
{
    use HasTranslations;

    protected $fillable = ['product_id', 'name', 'sort_order'];

    public array $translatable = ['name'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class)->orderBy('sort_order')->orderBy('id');
    }
}
