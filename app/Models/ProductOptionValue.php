<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * One selectable value of a product option, e.g. "16 A". price_delta is added to the product price.
 */
class ProductOptionValue extends Model
{
    use HasTranslations;

    protected $fillable = ['product_option_id', 'label', 'price_delta', 'sort_order'];

    public array $translatable = ['label'];

    protected $casts = ['price_delta' => 'float'];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
