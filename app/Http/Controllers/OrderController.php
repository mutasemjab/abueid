<?php

namespace App\Http\Controllers;

use App\Http\Requests\Front\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\CustomerAccount;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, OrderService $orders, CustomerAccount $accounts): JsonResponse
    {
        $data = $request->validated();
        $customer = $request->user('customer');

        // Opening an account and placing the order succeed or fail together.
        $order = DB::transaction(function () use ($request, $data, $orders, $accounts, &$customer) {
            if ($request->wantsAccount()) {
                $customer = $accounts->register($data['name'], $data['phone'], $data['password']);
            }

            return $orders->place($data, $customer);
        });

        if ($request->wantsAccount()) {
            Auth::guard('customer')->login($customer, true);
            $request->session()->regenerate();
        }

        return response()->json([
            ...OrderResource::make($order)->resolve($request),
            'account' => $customer ? $accounts->state($customer) : null,
        ], 201);
    }
}
