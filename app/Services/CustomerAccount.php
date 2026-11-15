<?php

namespace App\Services;

use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

/**
 * Storefront customer accounts: registration, saved addresses, favourites and the
 * "account state" handed to the storefront script.
 */
class CustomerAccount
{
    public function register(string $name, string $phone, string $password): Customer
    {
        return Customer::query()->create([
            'name' => $name,
            'phone' => $phone,
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Everything the "My account" page needs, resolved to the current locale.
     */
    public function state(Customer $customer): array
    {
        $customer->load([
            'orders' => fn ($query) => $query->latest()->with('items'),
            'addresses.deliveryArea',
            'favourites' => fn ($query) => $query->select('products.id', 'products.slug'),
        ]);

        return [
            'customer' => ['name' => $customer->name, 'phone' => $customer->phone],
            'orders' => OrderResource::collection($customer->orders)->resolve(),
            'addresses' => $customer->addresses->map(fn (CustomerAddress $address) => [
                'id' => $address->id,
                'area_id' => $address->delivery_area_id,
                'area' => $address->deliveryArea?->name,
                'address' => $address->address,
                'default' => $address->is_default,
            ])->values()->all(),
            'favourites' => $customer->favourites->pluck('slug')->values()->all(),
        ];
    }

    /**
     * Replace the customer's favourites with the given product slugs (unknown slugs are ignored).
     */
    public function syncFavourites(Customer $customer, array $slugs): void
    {
        $customer->favourites()->sync(
            Product::query()->whereIn('slug', $slugs)->pluck('id')
        );
    }

    /**
     * Add the given product slugs to the customer's favourites (used to keep favourites
     * that were collected before signing in).
     */
    public function mergeFavourites(Customer $customer, array $slugs): void
    {
        $customer->favourites()->syncWithoutDetaching(
            Product::query()->whereIn('slug', $slugs)->pluck('id')
        );
    }

    /**
     * Save a delivery address unless the customer already has the same one.
     */
    public function rememberAddress(Customer $customer, int $areaId, string $address): void
    {
        $customer->addresses()->firstOrCreate(
            ['delivery_area_id' => $areaId, 'address' => $address],
            ['is_default' => ! $customer->addresses()->exists()],
        );
    }

    public function saveAddress(Customer $customer, CustomerAddress $address, array $data): CustomerAddress
    {
        $address->fill([
            'delivery_area_id' => $data['area_id'],
            'address' => $data['address'],
        ]);
        $address->customer()->associate($customer);

        $hasOtherAddresses = $customer->addresses()
            ->when($address->exists, fn ($query) => $query->whereKeyNot($address->id))
            ->exists();

        $address->is_default = ($data['is_default'] ?? false) || ! $hasOtherAddresses;
        $address->save();

        if ($address->is_default) {
            $customer->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
        }

        return $address;
    }

    public function deleteAddress(Customer $customer, CustomerAddress $address): void
    {
        $address->delete();

        if ($address->is_default) {
            $customer->addresses()->first()?->update(['is_default' => true]);
        }
    }
}
