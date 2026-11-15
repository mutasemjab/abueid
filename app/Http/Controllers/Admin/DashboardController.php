<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => __('admin.dashboard.new_orders'), 'value' => Order::query()->where('status', OrderStatus::New)->count(), 'icon' => 'bi-bag-check'],
                ['label' => __('admin.dashboard.orders'), 'value' => Order::query()->count(), 'icon' => 'bi-receipt'],
                ['label' => __('admin.dashboard.products'), 'value' => Product::query()->count(), 'icon' => 'bi-box-seam'],
                ['label' => __('admin.dashboard.categories'), 'value' => Category::query()->count(), 'icon' => 'bi-grid'],
            ],
            'latestOrders' => Order::query()->latest()->limit(8)->get(),
            'lowStock' => Product::query()->active()->where('stock', '<=', 5)->orderBy('stock')->limit(8)->get(),
        ]);
    }
}
