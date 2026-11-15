<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Customer;
use App\Models\DeliveryArea;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Places storefront orders. Prices, stock and delivery fees are always taken from the
 * database — never from the client.
 */
class OrderService
{
    public function __construct(
        private readonly SiteSettings $settings,
        private readonly CustomerAccount $accounts,
    ) {
    }

    /**
     * @param  array{name: string, phone: string, address: string, notes: ?string, area_id: int, payment_method: string, items: list<array{id: string, qty: int}>}  $data
     */
    public function place(array $data, ?Customer $customer = null): Order
    {
        return DB::transaction(function () use ($data, $customer) {
            $quantities = collect($data['items'])
                ->groupBy('id')
                ->map(fn (Collection $lines) => (int) $lines->sum('qty'));

            $products = $this->availableProducts($quantities->keys()->all());
            $this->ensureStock($products, $quantities);

            $area = DeliveryArea::query()->active()->findOrFail($data['area_id']);
            $lines = $products->map(fn (Product $product) => $this->line($product, $quantities[$product->slug]));
            $subtotal = round($lines->sum('total'), 2);
            $fee = $this->deliveryFee($area, $subtotal);

            $order = Order::query()->create([
                'customer_id' => $customer?->id,
                'number' => $this->newNumber(),
                'customer_name' => $data['name'],
                'customer_phone' => $data['phone'],
                'address' => $data['address'],
                'notes' => $data['notes'] ?? null,
                'delivery_area_id' => $area->id,
                'area_name' => $area->name,
                'payment_method' => PaymentMethod::from($data['payment_method']),
                'subtotal' => $subtotal,
                'delivery_fee' => $fee,
                'total' => $subtotal + $fee,
                'status' => OrderStatus::New,
                'locale' => app()->getLocale(),
            ]);

            $order->items()->createMany($lines->values()->all());

            if ($customer) {
                $this->accounts->rememberAddress($customer, $area->id, $data['address']);
            }

            return $order->load('items');
        });
    }

    public function deliveryFee(DeliveryArea $area, float $subtotal): float
    {
        $freeFrom = (float) $this->settings->get('free_delivery_from', 0);

        return $freeFrom > 0 && $subtotal >= $freeFrom ? 0.0 : $area->fee;
    }

    /**
     * @return Collection<int, Product>
     */
    private function availableProducts(array $slugs): Collection
    {
        $products = Product::query()
            ->active()
            ->whereIn('slug', $slugs)
            ->whereHas('category', fn ($query) => $query->active())
            ->with('images')
            ->lockForUpdate()
            ->get();

        if ($products->count() !== count($slugs)) {
            throw ValidationException::withMessages(['items' => __('front.err_unavailable')]);
        }

        return $products;
    }

    private function ensureStock(Collection $products, Collection $quantities): void
    {
        $short = $products->first(fn (Product $product) => $quantities[$product->slug] > $product->stock);

        if ($short) {
            throw ValidationException::withMessages([
                'items' => __('front.err_stock', ['name' => $short->name, 'n' => $short->stock]),
            ]);
        }
    }

    private function line(Product $product, int $quantity): array
    {
        return [
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'image' => $product->images->first()?->path,
            'price' => $product->price,
            'quantity' => $quantity,
            'total' => round($product->price * $quantity, 2),
        ];
    }

    private function newNumber(): string
    {
        do {
            $number = 'AE-'.strtoupper(Str::random(6));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
