@extends('admin.layouts.app')
@section('title', __('admin.orders.title'))

@section('content')
    <x-admin.page-header :title="__('admin.orders.title')" :subtitle="__('admin.orders.subtitle')" />

    <x-admin.alerts />

    <x-admin.search :action="route('admin.orders.index')" :placeholder="__('admin.orders.search')">
        <div class="col-12 col-md-3">
            <select name="status" class="form-select form-select-sm no-select2">
                <option value="">{{ __('admin.orders.all_statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </x-admin.search>

    <div class="panel-card">
        <div class="panel-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.orders.number') }}</th>
                            <th>{{ __('admin.orders.customer') }}</th>
                            <th>{{ __('admin.orders.area') }}</th>
                            <th>{{ __('admin.orders.items') }}</th>
                            <th>{{ __('admin.orders.total') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.orders.date') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td dir="ltr"><code>{{ $order->number }}</code></td>
                                <td>
                                    <span class="fw-semibold d-block">{{ $order->customer_name }}</span>
                                    <small class="text-muted" dir="ltr">{{ $order->customer_phone }}</small>
                                </td>
                                <td>{{ $order->area_name }}</td>
                                <td>{{ $order->items_count }}</td>
                                <td dir="ltr">{{ number_format($order->total, 2) }}</td>
                                <td><span class="pill {{ $order->status->pill() }}">{{ $order->status->label() }}</span></td>
                                <td dir="ltr"><small>{{ $order->created_at->format('Y-m-d H:i') }}</small></td>
                                <td>
                                    <x-admin.row-actions
                                        :show="route('admin.orders.show', $order)"
                                        :destroy="auth('admin')->user()->can('order-delete') ? route('admin.orders.destroy', $order) : null" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="8" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($orders->hasPages())
            <div class="panel-card-body border-top pt-3">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
