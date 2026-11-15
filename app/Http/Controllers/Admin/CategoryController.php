<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use StoresImages;

    public function __construct()
    {
        $this->resourcePermissions('category');
    }

    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($request->query('search'), fn ($query, $search) => $query->where(fn ($query) => $query
                ->where('slug', 'like', "%{$search}%")
                ->orWhere('name->ar', 'like', "%{$search}%")
                ->orWhere('name->en', 'like', "%{$search}%")))
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->save(new Category(), $request);

        return redirect()->route('admin.categories.index')->with('success', __('admin.saved'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->save($category, $request);

        return redirect()->route('admin.categories.index')->with('success', __('admin.saved'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', __('admin.categories.has_products'));
        }

        $this->deleteImage($category->image);
        $category->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function save(Category $category, CategoryRequest $request): void
    {
        $category->fill($request->safe()->except(['image', 'remove_image']));
        $category->image = $this->uploadedImage($request, 'image', $category->image, 'categories');
        $category->save();
    }
}
