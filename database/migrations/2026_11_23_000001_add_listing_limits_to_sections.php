<?php

use App\Models\Section;
use Illuminate\Database\Migrations\Migration;

/**
 * Defaults for the new section fields on databases that were seeded before they existed:
 *  - categories: show 8 categories on the home page with a "view more" button
 *  - footer: list 4 categories
 * Existing values are never overwritten.
 */
return new class extends Migration
{
    private const DEFAULTS = [
        'categories' => [
            'cards_count' => 8,
            'more_label' => ['ar' => 'عرض المزيد', 'en' => 'View more'],
        ],
        'footer' => [
            'categories_count' => 4,
        ],
    ];

    public function up(): void
    {
        foreach (self::DEFAULTS as $key => $defaults) {
            $section = Section::query()->where('key', $key)->first();

            if ($section) {
                $section->update(['content' => ($section->content ?? []) + $defaults]);
            }
        }
    }

    public function down(): void
    {
        foreach (self::DEFAULTS as $key => $defaults) {
            $section = Section::query()->where('key', $key)->first();

            if ($section) {
                $section->update(['content' => array_diff_key($section->content ?? [], $defaults)]);
            }
        }
    }
};
