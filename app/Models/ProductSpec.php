<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProductSpec extends Model
{
    use HasTranslations;

    protected $fillable = ['product_id', 'label', 'value', 'sort_order'];

    public array $translatable = ['label', 'value'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * A value that is identical in every language (e.g. "230 V AC") is a technical
     * value and is rendered left-to-right.
     */
    public function isTechnical(): bool
    {
        return count(array_unique($this->getTranslations('value'))) <= 1;
    }
}
