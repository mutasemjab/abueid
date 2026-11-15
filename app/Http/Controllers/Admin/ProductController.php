<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
        $this->resourcePermissions('product');
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'brand', 'images'])
            ->when($request->query('category'), fn ($query, $category) => $query->where('category_id', $category))
            ->when($request->query('search'), fn ($query, $search) => $query->where(fn ($query) => $query
                ->where('sku', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('name->ar', 'like', "%{$search}%")
                ->orWhere('name->en', 'like', "%{$search}%")))
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        $categories = Category::query()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        return $this->form(new Product(['is_active' => true, 'stock' => 0]));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->save(new Product(), $request);

        return redirect()->route('admin.products.index')->with('success', __('admin.saved'));
    }

    public function edit(Product $product): View
    {
        return $this->form($product->load(['images', 'specs', 'pairs']));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->save($product, $request);

        return redirect()->route('admin.products.index')->with('success', __('admin.saved'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $paths = $product->images()->pluck('path');

        $product->delete();
        $paths->each(fn (string $path) => $this->media->delete($path));

        return back()->with('success', __('admin.deleted'));
    }

    private function form(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::query()->ordered()->get(),
            'brands' => Brand::query()->ordered()->get(),
            'others' => Product::query()->whereKeyNot($product->id)->ordered()->get(['id', 'sku', 'name']),
        ]);
    }

    private function save(Product $product, ProductRequest $request): void
    {
        DB::transaction(function () use ($product, $request) {
            $product->fill($request->safe()->except(['images', 'remove_images', 'specs', 'pairs']))->save();

            $this->removeImages($product, $request->validated('remove_images') ?? []);
            $this->addImages($product, $request->file('images') ?? []);
            $this->syncSpecs($product, $request->validated('specs') ?? []);
            $product->pairs()->sync($request->validated('pairs') ?? []);
        });
    }

    private function removeImages(Product $product, array $ids): void
    {
        $product->images()->whereKey($ids)->get()->each(function ($image) {
            $this->media->delete($image->path);
            $image->delete();
        });
    }

    private function addImages(Product $product, array $files): void
    {
        $position = (int) $product->images()->max('sort_order');

        foreach ($files as $file) {
            $product->images()->create([
                'path' => $this->media->store($file, 'products'),
                'sort_order' => ++$position,
            ]);
        }
    }

    private function syncSpecs(Product $product, array $specs): void
    {
        $product->specs()->delete();

        foreach (array_values($specs) as $position => $spec) {
            $product->specs()->create([
                'label' => $spec['label'],
                'value' => $spec['value'],
                'sort_order' => $position,
            ]);
        }
    }
}
