@extends('admin.layouts.app')
@section('title', $customer->name)

@section('content')
    <x-admin.page-header :title="$customer->name" :subtitle="$customer->phone">
        <a href="https://wa.me/{{ \App\Support\Phone::international($customer->phone) }}" target="_blank" rel="noopener" class="btn-outline-sm"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        <a href="{{ route('admin.customers.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <x-admin.card :title="__('admin.orders.title')" icon="bi-receipt" class="h-100">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.orders.number') }}</th>
                                <th>{{ __('admin.orders.items') }}</th>
                                <th>{{ __('admin.orders.total') }}</th>
                                <th>{{ __('admin.fields.status') }}</th>
                                <th>{{ __('admin.orders.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customer->orders as $order)
                                <tr>
                                    <td dir="ltr"><a href="{{ route('admin.orders.show', $order) }}"><code>{{ $order->number }}</code></a></td>
                                    <td>{{ $order->items_count }}</td>
                                    <td dir="ltr">{{ number_format($order->total, 2) }}</td>
                                    <td><span class="pill {{ $order->status->pill() }}">{{ $order->status->label() }}</span></td>
                                    <td dir="ltr"><small>{{ $order->created_at->format('Y-m-d H:i') }}</small></td>
                                </tr>
                            @empty
                                <x-admin.empty-row colspan="5" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>

        <div class="col-12 col-xl-4 d-flex flex-column gap-4">
            <x-admin.card :title="__('admin.customers.addresses')" icon="bi-geo-alt">
                @forelse ($customer->addresses as $address)
                    <div class="mb-2">
                        <span class="fw-semibold">{{ $address->deliveryArea?->name }}</span>
                        @if ($address->is_default)
                            <span class="pill pill-info ms-1">{{ __('admin.customers.default') }}</span>
                        @endif
                        <div class="text-muted small">{{ $address->address }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ __('admin.empty') }}</p>
                @endforelse
            </x-admin.card>

            @can('customer-edit')
                <x-admin.card :title="__('admin.customers.reset_password')" icon="bi-key">
                    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="d-flex flex-column gap-2">
                        @csrf
                        @method('PUT')
                        <x-admin.input name="password" type="text" :label="__('admin.customers.new_password')" required ltr autocomplete="off"
                                       :hint="__('admin.customers.reset_hint')" />
                        <div><button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('admin.save') }}</button></div>
                    </form>
                </x-admin.card>
            @endcan
        </div>
    </div>
@endsection
