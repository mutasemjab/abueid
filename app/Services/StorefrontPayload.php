<?php

namespace App\Services;

use App\Http\Resources\ProductCardResource;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeliveryArea;
use App\Models\Product;
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
        private readonly CustomerAccount $accounts,
        private readonly Catalog $catalog,
    ) {
    }

    /**
     * The catalogue itself is too large for the page: only counts, the price range and the
     * featured products are embedded; the rest is loaded from the products API.
     */
    public function build(): array
    {
        $stats = $this->catalogStats();

        return [
            'locale' => app()->getLocale(),
            'i18n' => trans('front'),
            'store' => $this->store($stats),
            'categories' => $this->storefront->categories()
                ->map(fn (Category $category) => $this->category($category, $stats))->values(),
            'brands' => Brand::query()->active()->ordered()->get()
                ->map(fn (Brand $brand) => $this->brand($brand, $stats))->values(),
            'featured' => ProductCardResource::collection($this->catalog->featured())->resolve(),
            'branches' => Branch::query()->active()->ordered()->get()->map(fn (Branch $branch) => $this->branch($branch))->values(),
            'assurances' => $this->assurances(),
            'account' => $this->account(),
        ];
    }

    /**
     * Sellable product counts per category / brand and the price range.
     */
    private function catalogStats(): array
    {
        $sellable = fn () => Product::query()->sellable()->toBase();

        $range = $sellable()->selectRaw('count(*) as total, min(price) as min_price, max(price) as max_price')->first();

        return [
            'total' => (int) $range->total,
            'min' => (float) $range->min_price,
            'max' => (float) $range->max_price,
            'categories' => $sellable()->groupBy('category_id')->selectRaw('category_id, count(*) as n')->pluck('n', 'category_id'),
            'brands' => $sellable()->whereNotNull('brand_id')->groupBy('brand_id')->selectRaw('brand_id, count(*) as n')->pluck('n', 'brand_id'),
        ];
    }

    /**
     * Signed-in customer state (null for guests) and the account endpoints.
     */
    private function account(): array
    {
        $customer = auth('customer')->user();

        return [
            'state' => $customer ? $this->accounts->state($customer) : null,
            'urls' => [
                'login' => route('account.login'),
                'register' => route('account.register'),
                'logout' => route('account.logout'),
                'profile' => route('account.profile'),
                'favourites' => route('account.favourites'),
                'addresses' => route('account.addresses.store'),
            ],
        ];
    }

    private function store(array $stats): array
    {
        return [
            'productCount' => $stats['total'],
            'priceMin' => floor($stats['min']),
            'priceMax' => ceil($stats['max']),
            'productsUrl' => route('products.index'),
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

    private function category(Category $category, array $stats): array
    {
        return [
            'id' => $category->slug,
            'name' => $category->name,
            'blurb' => $category->blurb,
            'subs' => array_slice($category->subcategoryList(), 0, 8),
            'img' => media_url($category->image),
            'count' => (int) ($stats['categories'][$category->id] ?? 0),
            'ghost' => Str::before((string) $category->getTranslation('name', 'en'), ' '),
        ];
    }

    private function brand(Brand $brand, array $stats): array
    {
        return [
            'id' => $brand->slug,
            'name' => $brand->name,
            'logo' => media_url($brand->logo),
            'origin' => $brand->origin,
            'line' => $brand->product_line,
            'count' => (int) ($stats['brands'][$brand->id] ?? 0),
        ];
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
