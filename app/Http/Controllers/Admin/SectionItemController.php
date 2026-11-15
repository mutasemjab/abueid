<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SectionItemRequest;
use App\Models\Section;
use App\Models\SectionItem;
use App\Services\MediaService;
use App\Support\ContentSchema;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SectionItemController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
        $this->middleware($this->perm('section-edit'));
    }

    public function create(Section $section): View
    {
        abort_unless($section->hasItems(), 404);

        $item = new SectionItem([
            'is_active' => true,
            'sort_order' => (int) $section->items()->max('sort_order') + 1,
        ]);

        return view('admin.sections.item-form', compact('section', 'item'));
    }

    public function store(SectionItemRequest $request, Section $section): RedirectResponse
    {
        abort_unless($section->hasItems(), 404);

        $this->save($section->items()->make(), $request);

        return redirect()->route('admin.sections.edit', $section)->with('success', __('admin.saved'));
    }

    public function edit(Section $section, SectionItem $item): View
    {
        return view('admin.sections.item-form', compact('section', 'item'));
    }

    public function update(SectionItemRequest $request, Section $section, SectionItem $item): RedirectResponse
    {
        $this->save($item, $request);

        return redirect()->route('admin.sections.edit', $section)->with('success', __('admin.saved'));
    }

    public function destroy(Section $section, SectionItem $item): RedirectResponse
    {
        (new ContentSchema($section->itemFields()))->deleteImages($item->content ?? [], $this->media);
        $item->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function save(SectionItem $item, SectionItemRequest $request): void
    {
        $item->content = $request->schema()->apply($item->content ?? [], $request, $this->media, "sections/{$item->section->key}");
        $item->fill($request->safe()->only(['sort_order', 'is_active']));
        $item->save();
    }
}
