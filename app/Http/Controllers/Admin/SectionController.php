<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SectionRequest;
use App\Models\Section;
use App\Services\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware($this->perm('section-table'))->only('index');
        $this->middleware($this->perm('section-edit'))->only(['edit', 'update']);
    }

    public function index(): View
    {
        $sections = Section::query()
            ->withCount('items')
            ->ordered()
            ->get()
            ->filter(fn (Section $section) => $section->schema() !== []);

        return view('admin.sections.index', compact('sections'));
    }

    public function edit(Section $section): View
    {
        abort_if($section->schema() === [], 404);

        return view('admin.sections.edit', ['section' => $section->load('items')]);
    }

    public function update(SectionRequest $request, Section $section, MediaService $media): RedirectResponse
    {
        $section->content = $request->schema()->apply($section->content ?? [], $request, $media, "sections/{$section->key}");
        $section->fill($request->safe()->only(['sort_order', 'is_active']));
        $section->show_in_menu = $section->canBeInMenu() && $request->boolean('show_in_menu');
        $section->save();

        return redirect()->route('admin.sections.edit', $section)->with('success', __('admin.saved'));
    }
}
