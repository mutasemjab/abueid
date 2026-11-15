<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\LoginRequest;
use App\Http\Requests\Front\RegisterRequest;
use App\Services\CustomerAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Storefront sign-up / sign-in (mobile number + password). JSON endpoints used by app.js.
 */
class AuthController extends Controller
{
    public function __construct(private readonly CustomerAccount $accounts)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $customer = $this->accounts->register(
            $request->validated('name'),
            $request->validated('phone'),
            $request->validated('password'),
        );

        Auth::guard('customer')->login($customer, true);
        $request->session()->regenerate();
        $this->accounts->mergeFavourites($customer, (array) $request->input('favourites', []));

        return response()->json($this->accounts->state($customer), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->safe()->only(['phone', 'password']);

        if (! Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['phone' => __('front.acc_failed')]);
        }

        $request->session()->regenerate();

        $customer = Auth::guard('customer')->user();
        $this->accounts->mergeFavourites($customer, (array) $request->input('favourites', []));

        return response()->json($this->accounts->state($customer));
    }

    public function logout(Request $request): Response
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
