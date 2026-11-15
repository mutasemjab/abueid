<?php

namespace App\Models;

use App\Models\Concerns\HasSchemaContent;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A block of the website. Its editable fields are declared in config/sections.php.
 */
class Section extends Model
{
    use HasSchemaContent, Publishable;

    protected $fillable = ['key', 'content', 'is_active', 'show_in_menu', 'sort_order'];

    protected $casts = ['show_in_menu' => 'boolean'];

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }

    public function schema(): array
    {
        return config("sections.{$this->key}", []);
    }

    public function fields(): array
    {
        return $this->schema()['fields'] ?? [];
    }

    public function itemFields(): array
    {
        return $this->schema()['items']['fields'] ?? [];
    }

    public function hasItems(): bool
    {
        return $this->itemFields() !== [];
    }

    public function isOnHomePage(): bool
    {
        return (bool) ($this->schema()['home'] ?? false);
    }

    public function canBeInMenu(): bool
    {
        return (bool) ($this->schema()['menu'] ?? false);
    }

    public function isNumbered(): bool
    {
        return (bool) ($this->schema()['numbered'] ?? false);
    }

    public function anchor(): ?string
    {
        return $this->schema()['anchor'] ?? null;
    }

    public function title(): string
    {
        return __("sections.names.{$this->key}");
    }
}
