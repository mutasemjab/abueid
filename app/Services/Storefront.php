<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Section;
use Illuminate\Support\Collection;

/**
 * Read model of the storefront layout: which sections are visible, in which order,
 * and which of them appear in the menu. Loaded once per request.
 */
class Storefront
{
    private ?Collection $sections = null;

    private ?Collection $categories = null;

    /**
     * Active sections keyed by their key, in display order.
     *
     * @return Collection<string, Section>
     */
    public function sections(): Collection
    {
        return $this->sections ??= Section::query()
            ->active()
            ->ordered()
            ->with('activeItems')
            ->get()
            ->filter(fn (Section $section) => $section->schema() !== [])
            ->keyBy('key');
    }

    public function section(string $key): ?Section
    {
        return $this->sections()->get($key);
    }

    public function has(string $key): bool
    {
        return $this->sections()->has($key);
    }

    /** @return Collection<string, Section> */
    public function homeSections(): Collection
    {
        return $this->sections()->filter(fn (Section $section) => $section->isOnHomePage());
    }

    /** @return Collection<string, Section> */
    public function menuSections(): Collection
    {
        return $this->homeSections()->filter(
            fn (Section $section) => $section->canBeInMenu() && $section->show_in_menu && $section->anchor()
        );
    }

    /** @return Collection<int, Category> */
    public function categories(): Collection
    {
        return $this->categories ??= Category::query()->active()->ordered()->get();
    }
}
