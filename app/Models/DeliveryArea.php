<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DeliveryArea extends Model
{
    use HasTranslations, Publishable;

    protected $fillable = ['name', 'fee', 'sort_order', 'is_active'];

    public array $translatable = ['name'];

    protected $casts = ['fee' => 'float'];
}
