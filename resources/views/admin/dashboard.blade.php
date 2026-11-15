@extends('admin.layouts.app')
@section('title', __('messages.dashboard'))

@section('content')
    <x-admin.page-header :title="__('messages.dashboard')" :subtitle="__('admin.dashboard.subtitle')">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-outline-sm"><i class="bi bi-box-arrow-up-right"></i> {{ __('admin.dashboard.view_site') }}</a>
    </x-admin.page-header>

    <div class="row g-3 mb-4">
        @foreach ($stats as $stat)
            <div class="col-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--accent-soft);color:var(--accent-dark)"><i class="bi {{ $stat['icon'] }}"></i></div>
                    <div class="stat-value">{{ $stat['value'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <x-admin.card :title="__('admin.dashboard.latest_orders')" icon="bi-receipt" class="h-100">
                <div class="table-responsive">
                    <table class="data-table">
                        <tbody>
                            @forelse ($latestOrders as $order)
                                <tr>
                                    <td dir="ltr"><a href="{{ route('admin.orders.show', $order) }}"><code>{{ $order->number }}</code></a></td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td dir="ltr">{{ number_format($order->total, 2) }}</td>
                                    <td><span class="pill {{ $order->status->pill() }}">{{ $order->status->label() }}</span></td>
                                </tr>
                            @empty
                                <x-admin.empty-row colspan="4" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>
        <div class="col-12 col-xl-5">
            <x-admin.card :title="__('admin.dashboard.low_stock')" icon="bi-exclamation-triangle" class="h-100">
                <div class="table-responsive">
                    <table class="data-table">
                        <tbody>
                            @forelse ($lowStock as $product)
                                <tr>
                                    <td><a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a></td>
                                    <td dir="ltr"><code>{{ $product->sku }}</code></td>
                                    <td><span class="pill {{ $product->stock === 0 ? 'pill-danger' : 'pill-warning' }}">{{ $product->stock }}</span></td>
                                </tr>
                            @empty
                                <x-admin.empty-row colspan="3" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>
    </div>
@endsection
