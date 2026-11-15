<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations, Publishable;

    protected $fillable = ['slug', 'name', 'blurb', 'subcategories', 'image', 'sort_order', 'is_active'];

    public array $translatable = ['name', 'blurb', 'subcategories'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Sub-categories are edited as one entry per line.
     *
     * @return list<string>
     */
    public function subcategoryList(?string $locale = null): array
    {
        $text = (string) $this->getTranslation('subcategories', $locale ?? app()->getLocale());

        return array_values(array_filter(array_map('trim', preg_split('/\R/u', $text) ?: [])));
    }
}
