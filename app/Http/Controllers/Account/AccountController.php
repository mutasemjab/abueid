<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AddressRequest;
use App\Http\Requests\Front\FavouritesRequest;
use App\Http\Requests\Front\ProfileRequest;
use App\Models\CustomerAddress;
use App\Services\CustomerAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * "My account": profile, saved addresses and favourites of the signed-in customer.
 * Every endpoint returns the full account state.
 */
class AccountController extends Controller
{
    public function __construct(private readonly CustomerAccount $accounts)
    {
    }

    public function show(Request $request): JsonResponse
    {
        return $this->state($request);
    }

    public function updateProfile(ProfileRequest $request): JsonResponse
    {
        $customer = $request->user('customer');
        $customer->name = $request->validated('name');

        if ($request->filled('password')) {
            $customer->password = Hash::make($request->validated('password'));
        }

        $customer->save();

        return $this->state($request);
    }

    public function syncFavourites(FavouritesRequest $request): JsonResponse
    {
        $this->accounts->syncFavourites($request->user('customer'), $request->validated('ids'));

        return $this->state($request);
    }

    public function storeAddress(AddressRequest $request): JsonResponse
    {
        $this->accounts->saveAddress($request->user('customer'), new CustomerAddress(), $request->validated());

        return $this->state($request, 201);
    }

    public function updateAddress(AddressRequest $request, int $address): JsonResponse
    {
        $customer = $request->user('customer');
        $this->accounts->saveAddress($customer, $customer->addresses()->findOrFail($address), $request->validated());

        return $this->state($request);
    }

    public function destroyAddress(Request $request, int $address): JsonResponse
    {
        $customer = $request->user('customer');
        $this->accounts->deleteAddress($customer, $customer->addresses()->findOrFail($address));

        return $this->state($request);
    }

    private function state(Request $request, int $status = 200): JsonResponse
    {
        return response()->json($this->accounts->state($request->user('customer')->fresh()), $status);
    }
}
