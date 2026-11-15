@extends('admin.layouts.app')
@section('title', __('admin.orders.order').' '.$order->number)

@php($whatsappNumber = preg_replace('/^0/', '962', $order->customer_phone))

@section('content')
    <x-admin.page-header :title="__('admin.orders.order').' '.$order->number" :subtitle="$order->created_at->format('Y-m-d H:i')">
        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="btn-outline-sm"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        <a href="{{ route('admin.orders.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <x-admin.card :title="__('admin.orders.items')" icon="bi-bag" class="h-100">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.fields.name') }}</th>
                                <th>{{ __('admin.fields.sku') }}</th>
                                <th>{{ __('admin.fields.price') }}</th>
                                <th>{{ __('admin.orders.quantity') }}</th>
                                <th>{{ __('admin.orders.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="d-flex align-items-center gap-2">
                                        @if ($item->image)
                                            <img src="{{ media_url($item->image) }}" alt="" class="admin-thumb">
                                        @endif
                                        <span class="fw-semibold">{{ $item->name }}</span>
                                    </td>
                                    <td dir="ltr"><code>{{ $item->sku }}</code></td>
                                    <td dir="ltr">{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td dir="ltr">{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th colspan="4">{{ __('admin.orders.subtotal') }}</th><td dir="ltr">{{ number_format($order->subtotal, 2) }}</td></tr>
                            <tr><th colspan="4">{{ __('admin.orders.delivery_fee') }}</th><td dir="ltr">{{ number_format($order->delivery_fee, 2) }}</td></tr>
                            <tr><th colspan="4">{{ __('admin.orders.total') }}</th><td dir="ltr" class="fw-bold">{{ number_format($order->total, 2) }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </x-admin.card>
        </div>

        <div class="col-12 col-xl-4 d-flex flex-column gap-4">
            <x-admin.card :title="__('admin.orders.customer')" icon="bi-person">
                <dl class="mb-0">
                    <dt>{{ __('admin.fields.name') }}</dt><dd>{{ $order->customer_name }}</dd>
                    <dt>{{ __('admin.fields.phone') }}</dt><dd dir="ltr" class="text-start">{{ $order->customer_phone }}</dd>
                    <dt>{{ __('admin.orders.area') }}</dt><dd>{{ $order->area_name }}</dd>
                    <dt>{{ __('admin.fields.address') }}</dt><dd>{{ $order->address }}</dd>
                    <dt>{{ __('admin.orders.payment') }}</dt><dd>{{ $order->payment_method->label() }}</dd>
                    @if ($order->notes)
                        <dt>{{ __('admin.orders.notes') }}</dt><dd class="mb-0">{{ $order->notes }}</dd>
                    @endif
                </dl>
            </x-admin.card>

            @can('order-edit')
                <x-admin.card :title="__('admin.fields.status')" icon="bi-flag">
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-flex gap-2">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select no-select2">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i></button>
                    </form>
                </x-admin.card>
            @endcan
        </div>
    </div>
@endsection
