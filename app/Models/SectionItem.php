<?php

namespace App\Models;

use App\Models\Concerns\HasSchemaContent;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionItem extends Model
{
    use HasSchemaContent, Publishable;

    protected $fillable = ['section_id', 'content', 'sort_order', 'is_active'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
