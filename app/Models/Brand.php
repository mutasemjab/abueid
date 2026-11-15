<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
{
    use HasTranslations, Publishable;

    protected $fillable = ['slug', 'name', 'logo', 'origin', 'product_line', 'sort_order', 'is_active'];

    public array $translatable = ['origin', 'product_line'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
