<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Branch extends Model
{
    use HasTranslations, Publishable;

    protected $fillable = ['name', 'city', 'address', 'hours', 'phone', 'map_url', 'photo', 'is_main', 'sort_order', 'is_active'];

    public array $translatable = ['name', 'city', 'address', 'hours'];

    protected $casts = ['is_main' => 'boolean'];
}
