<?php

namespace App\Http\Controllers;

use App\Http\Requests\Front\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, OrderService $orders): JsonResponse
    {
        $order = $orders->place($request->validated());

        return OrderResource::make($order)->response()->setStatusCode(201);
    }
}
