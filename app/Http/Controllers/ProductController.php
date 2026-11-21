<?php

namespace App\Http\Controllers;

use App\Http\Requests\Front\ProductIndexRequest;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductDetailResource;
use App\Services\Catalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Storefront catalogue API used by assets_front/js/app.js.
 */
class ProductController extends Controller
{
    public function __construct(private readonly Catalog $catalog)
    {
    }

    /**
     * Filtered, sorted and paginated product cards. With "ids" (cart / favourites),
     * returns exactly those products without pagination.
     */
    public function index(ProductIndexRequest $request): JsonResponse
    {
        $query = $this->catalog->cards();

        if ($ids = $request->list('ids')) {
            return response()->json(['data' => ProductCardResource::collection($query->whereIn('slug', $ids)->get())->resolve()]);
        }

        $query
            ->when($request->list('cat'), fn (Builder $q, array $slugs) => $q->whereHas('category', fn (Builder $c) => $c->whereIn('slug', $slugs)))
            ->when($request->list('brand'), fn (Builder $q, array $slugs) => $q->whereHas('brand', fn (Builder $b) => $b->whereIn('slug', $slugs)))
            ->when($request->validated('q'), fn (Builder $q, string $term) => $q->search($term))
            ->when($request->validated('max') !== null, fn (Builder $q) => $q->where('price', '<=', (float) $request->validated('max')))
            ->when($request->boolean('in_stock'), fn (Builder $q) => $q->where(fn (Builder $s) => $s->whereNull('stock')->orWhere('stock', '>', 0)));

        match ($request->validated('sort') ?? 'feat') {
            'low' => $query->orderBy('price')->orderBy('id'),
            'high' => $query->orderByDesc('price')->orderBy('id'),
            default => $this->catalog->popularFirst($query),
        };

        $page = $query->paginate($request->perPage());

        return response()->json([
            'data' => ProductCardResource::collection($page->getCollection())->resolve(),
            'meta' => ['total' => $page->total(), 'page' => $page->currentPage(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $product = $this->catalog->cards()->with('specs')->where('slug', $slug)->firstOrFail();
        $pairs = $this->catalog->pairsOf($product);

        return response()->json(
            ProductDetailResource::make($product)
                ->withLinks($pairs, $this->catalog->relatedTo($product, $pairs))
                ->resolve($request)
        );
    }
}
