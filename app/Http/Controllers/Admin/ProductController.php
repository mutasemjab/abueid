<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
        $this->resourcePermissions('product');
        $this->middleware($this->perm('product-edit'))->only('search');
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'brand', 'images'])
            ->when($request->query('category'), fn ($query, $category) => $query->where('category_id', $category))
            ->when($request->query('status') === 'visible', fn ($query) => $query->where('is_active', true))
            ->when($request->query('status') === 'hidden', fn ($query) => $query->where('is_active', false))
            ->when($request->query('status') === 'unpriced', fn ($query) => $query->where('price', '<=', 0))
            ->when($request->query('search'), fn ($query, $search) => $query->search($search))
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        $categories = Category::query()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        return $this->form(new Product(['is_active' => true, 'tax_rate' => 16]));
    }

    /**
     * Product picker for the "pairs with" select (searches as the admin types).
     */
    public function search(Request $request): JsonResponse
    {
        $results = Product::query()
            ->when($request->query('q'), fn ($query, $term) => $query->search($term))
            ->whereKeyNot((int) $request->query('except'))
            ->orderBy('id')
            ->limit(20)
            ->get(['id', 'sku', 'name']);

        return response()->json([
            'results' => $results->map(fn (Product $product) => ['id' => $product->id, 'text' => "{$product->sku} — {$product->name}"]),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->save(new Product(), $request);

        return redirect()->route('admin.products.index')->with('success', __('admin.saved'));
    }

    public function edit(Product $product): View
    {
        return $this->form($product->load(['images', 'specs', 'pairs', 'options.values']));
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
        $selectedPairs = array_map('intval', (array) old('pairs', $product->pairs->modelKeys()));

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::query()->ordered()->get(),
            'brands' => Brand::query()->ordered()->get(),
            'pairOptions' => Product::query()->whereKey($selectedPairs)->get(['id', 'sku', 'name']),
        ]);
    }

    private function save(Product $product, ProductRequest $request): void
    {
        DB::transaction(function () use ($product, $request) {
            $product->fill($request->safe()->except(['images', 'remove_images', 'specs', 'pairs', 'options']))->save();

            $this->removeImages($product, $request->validated('remove_images') ?? []);
            $this->addImages($product, $request->file('images') ?? []);
            $this->syncSpecs($product, $request->validated('specs') ?? []);
            $this->syncOptions($product, $request->validated('options') ?? []);
            $product->pairs()->sync($request->validated('pairs') ?? []);
        });
    }

    /**
     * Update options and values in place (keeping their ids, so items already in customers'
     * carts stay valid), create the new ones and delete the ones removed from the form.
     */
    private function syncOptions(Product $product, array $options): void
    {
        $keptOptions = [];

        foreach (array_values($options) as $position => $data) {
            $option = $product->options()->find($data['id'] ?? null) ?? $product->options()->make();
            $option->fill(['name' => $data['name'], 'sort_order' => $position])->save();
            $keptOptions[] = $option->id;

            $keptValues = [];
            foreach (array_values($data['values']) as $index => $valueData) {
                $value = $option->values()->find($valueData['id'] ?? null) ?? $option->values()->make();
                $value->fill([
                    'label' => $valueData['label'],
                    'price_delta' => (float) ($valueData['price'] ?? 0),
                    'sort_order' => $index,
                ])->save();
                $keptValues[] = $value->id;
            }

            $option->values()->whereKeyNot($keptValues)->delete();
        }

        $product->options()->whereKeyNot($keptOptions)->delete();
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
