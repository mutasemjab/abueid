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
     * Cart lines are a product (slug), a quantity and the chosen option values ({option id: value id}).
     * The same product with different options makes separate order lines.
     *
     * @param  array{name: string, phone: string, address: string, notes: ?string, area_id: int, payment_method: string, items: list<array{id: string, qty: int, options?: array<int|string, int|string>}>}  $data
     */
    public function place(array $data, ?Customer $customer = null): Order
    {
        return DB::transaction(function () use ($data, $customer) {
            $cart = $this->mergeLines($data['items']);
            $products = $this->availableProducts($cart->pluck('id')->unique()->values()->all())->keyBy('slug');
            $this->ensureStock($products, $cart->groupBy('id')->map(fn (Collection $lines) => $lines->sum('qty')));

            $area = DeliveryArea::query()->active()->findOrFail($data['area_id']);
            $lines = $cart->map(fn (array $line) => $this->line($products[$line['id']], $line['qty'], $line['options']));
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
     * Normalise the cart: integer option ids, identical lines (same product and options) merged.
     *
     * @return Collection<int, array{id: string, qty: int, options: array<int, int>}>
     */
    private function mergeLines(array $items): Collection
    {
        return collect($items)
            ->map(function (array $item) {
                $options = collect($item['options'] ?? [])->mapWithKeys(fn ($value, $option) => [(int) $option => (int) $value])->sortKeys()->all();

                return ['id' => (string) $item['id'], 'qty' => (int) $item['qty'], 'options' => $options];
            })
            ->groupBy(fn (array $line) => $line['id'].'|'.json_encode($line['options']))
            ->map(fn (Collection $same) => [...$same->first(), 'qty' => $same->sum('qty')])
            ->values();
    }

    /**
     * @return Collection<int, Product>
     */
    private function availableProducts(array $slugs): Collection
    {
        $products = Product::query()
            ->sellable()
            ->whereIn('slug', $slugs)
            ->with(['images', 'options.values'])
            ->lockForUpdate()
            ->get();

        if ($products->count() !== count($slugs)) {
            throw ValidationException::withMessages(['items' => __('front.err_unavailable')]);
        }

        return $products;
    }

    private function ensureStock(Collection $products, Collection $quantities): void
    {
        $short = $products->first(fn (Product $product) => $product->tracksStock() && $quantities[$product->slug] > $product->stock);

        if ($short) {
            throw ValidationException::withMessages([
                'items' => __('front.err_stock', ['name' => $short->name, 'n' => $short->stock]),
            ]);
        }
    }

    /**
     * @param  array<int, int>  $selection  option id => value id
     */
    private function line(Product $product, int $quantity, array $selection): array
    {
        $chosen = $this->chosenValues($product, $selection);
        $price = round($product->price + $chosen->sum('price_delta'), 2);

        return [
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'options' => $chosen->isEmpty() ? null : $chosen->map(fn ($value) => [
                'name' => $value->option->name,
                'value' => $value->label,
            ])->values()->all(),
            'image' => $product->images->first()?->path,
            'price' => $price,
            'quantity' => $quantity,
            'total' => round($price * $quantity, 2),
        ];
    }

    /**
     * The chosen value of every option of the product. Every option needs exactly one of its
     * own values; anything else (missing, foreign or extra choices) is rejected.
     *
     * @param  array<int, int>  $selection  option id => value id
     * @return Collection<int, \App\Models\ProductOptionValue>
     */
    private function chosenValues(Product $product, array $selection): Collection
    {
        $extra = array_diff(array_keys($selection), $product->options->modelKeys());

        $chosen = $product->options->map(function ($option) use ($selection, $product) {
            $value = $option->values->firstWhere('id', $selection[$option->id] ?? null);

            if (! $value) {
                throw ValidationException::withMessages([
                    'items' => __('front.err_option', ['option' => $option->name, 'name' => $product->name]),
                ]);
            }

            return $value->setRelation('option', $option);
        });

        if ($extra) {
            throw ValidationException::withMessages(['items' => __('front.err_unavailable')]);
        }

        return $chosen->values();
    }

    private function newNumber(): string
    {
        do {
            $number = 'AE-'.strtoupper(Str::random(6));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
