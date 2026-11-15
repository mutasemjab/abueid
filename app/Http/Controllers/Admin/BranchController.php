<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BranchRequest;
use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BranchController extends Controller
{
    use StoresImages;

    public function __construct()
    {
        $this->resourcePermissions('branch');
    }

    public function index(): View
    {
        $branches = Branch::query()->ordered()->paginate(20);

        return view('admin.branches.index', compact('branches'));
    }

    public function create(): View
    {
        return view('admin.branches.form', ['branch' => new Branch()]);
    }

    public function store(BranchRequest $request): RedirectResponse
    {
        $this->save(new Branch(), $request);

        return redirect()->route('admin.branches.index')->with('success', __('admin.saved'));
    }

    public function edit(Branch $branch): View
    {
        return view('admin.branches.form', compact('branch'));
    }

    public function update(BranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->save($branch, $request);

        return redirect()->route('admin.branches.index')->with('success', __('admin.saved'));
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->deleteImage($branch->photo);
        $branch->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function save(Branch $branch, BranchRequest $request): void
    {
        $branch->fill($request->safe()->except(['photo', 'remove_photo']));
        $branch->photo = $this->uploadedImage($request, 'photo', $branch->photo, 'branches');
        $branch->save();
    }
}
