<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeliveryArea;
use App\Models\Product;
use App\Models\ProductSpec;
use App\Models\SectionItem;
use Illuminate\Support\Str;

/**
 * Data handed to the storefront script (assets_front/js/app.js), already resolved
 * to the current locale.
 */
class StorefrontPayload
{
    public function __construct(
        private readonly Storefront $storefront,
        private readonly SiteSettings $settings,
    ) {
    }

    public function build(): array
    {
        $categories = $this->storefront->categories();
        $brands = Brand::query()->active()->ordered()->get();

        return [
            'locale' => app()->getLocale(),
            'i18n' => trans('front'),
            'store' => $this->store(),
            'categories' => $categories->map(fn (Category $category) => $this->category($category))->values(),
            'brands' => $brands->map(fn (Brand $brand) => $this->brand($brand))->values(),
            'products' => $this->products($categories->pluck('id')->all(), $brands->pluck('id')->all()),
            'branches' => Branch::query()->active()->ordered()->get()->map(fn (Branch $branch) => $this->branch($branch))->values(),
            'assurances' => $this->assurances(),
        ];
    }

    private function store(): array
    {
        return [
            'whatsapp' => (string) $this->settings->get('whatsapp', ''),
            'cliqAlias' => (string) $this->settings->get('cliq_alias', ''),
            'freeDeliveryFrom' => (float) $this->settings->get('free_delivery_from', 0),
            'since' => $this->settings->get('founded_year') ? (int) $this->settings->get('founded_year') : null,
            'currency' => (string) $this->settings->get('currency', ''),
            'placeholder' => $this->settings->imageUrl('empty_state_image'),
            'orderUrl' => route('orders.store'),
            'areas' => DeliveryArea::query()->active()->ordered()->get()->map(fn (DeliveryArea $area) => [
                'id' => $area->id,
                'name' => $area->name,
                'fee' => $area->fee,
            ])->values(),
        ];
    }

    private function category(Category $category): array
    {
        return [
            'id' => $category->slug,
            'name' => $category->name,
            'blurb' => $category->blurb,
            'subs' => $category->subcategoryList(),
            'img' => media_url($category->image),
            'ghost' => Str::before((string) $category->getTranslation('name', 'en'), ' '),
        ];
    }

    private function brand(Brand $brand): array
    {
        return [
            'id' => $brand->slug,
            'name' => $brand->name,
            'logo' => media_url($brand->logo),
            'origin' => $brand->origin,
            'line' => $brand->product_line,
        ];
    }

    private function products(array $categoryIds, array $brandIds): array
    {
        return Product::query()
            ->active()
            ->ordered()
            ->whereIn('category_id', $categoryIds)
            ->with(['category:id,slug', 'brand', 'images', 'specs', 'pairs' => fn ($query) => $query->select('products.id', 'products.slug')])
            ->get()
            ->map(function (Product $product) use ($brandIds) {
                $brand = in_array($product->brand_id, $brandIds, true) ? $product->brand : null;

                return [
                    'id' => $product->slug,
                    'sku' => $product->sku,
                    'cat' => $product->category->slug,
                    'sub' => $product->subcategory,
                    'brand' => $brand?->slug,
                    'brandName' => $brand?->name,
                    'series' => $product->series,
                    'name' => $product->name,
                    'short' => $product->short_description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'min' => $product->range_min,
                    'max' => $product->range_max,
                    'images' => $product->images->map->url()->values(),
                    'specs' => $product->specs->map(fn (ProductSpec $spec) => [
                        'label' => $spec->label,
                        'value' => $spec->value,
                        'ltr' => $spec->isTechnical(),
                    ])->values(),
                    'pairs' => $product->pairs->pluck('slug')->values(),
                    'search' => mb_strtolower(implode(' ', [
                        $product->sku,
                        $product->series,
                        $brand?->name,
                        ...array_values($product->getTranslations('name')),
                        ...array_values($product->getTranslations('short_description')),
                    ])),
                ];
            })
            ->values()
            ->all();
    }

    private function branch(Branch $branch): array
    {
        return [
            'name' => $branch->name,
            'city' => $branch->city,
            'address' => $branch->address,
            'hours' => $branch->hours,
            'phone' => $branch->phone,
            'map' => $branch->map_url,
            'photo' => media_url($branch->photo),
            'main' => $branch->is_main,
        ];
    }

    private function assurances(): array
    {
        return $this->storefront->section('product_page')?->activeItems
            ->map(fn (SectionItem $item) => [
                'title' => $item->value('title'),
                'text' => $item->value('description'),
            ])
            ->values()
            ->all() ?? [];
    }
}
