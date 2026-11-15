@extends('admin.layouts.app')
@section('title', __('admin.customers.title'))

@section('content')
    <x-admin.page-header :title="__('admin.customers.title')" :subtitle="__('admin.customers.subtitle')" />

    <x-admin.alerts />
    <x-admin.search :action="route('admin.customers.index')" :placeholder="__('admin.customers.search')" />

    <div class="panel-card">
        <div class="panel-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.fields.name') }}</th>
                            <th>{{ __('admin.fields.phone') }}</th>
                            <th>{{ __('admin.orders.title') }}</th>
                            <th>{{ __('admin.customers.spent') }}</th>
                            <th>{{ __('admin.customers.joined') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="fw-semibold">{{ $customer->name }}</td>
                                <td dir="ltr">{{ $customer->phone }}</td>
                                <td>{{ $customer->orders_count }}</td>
                                <td dir="ltr">{{ number_format((float) $customer->orders_sum_total, 2) }}</td>
                                <td dir="ltr"><small>{{ $customer->created_at->format('Y-m-d') }}</small></td>
                                <td>
                                    <x-admin.row-actions
                                        :show="route('admin.customers.show', $customer)"
                                        :destroy="auth('admin')->user()->can('customer-delete') ? route('admin.customers.destroy', $customer) : null" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="6" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($customers->hasPages())
            <div class="panel-card-body border-top pt-3">{{ $customers->links() }}</div>
        @endif
    </div>
@endsection
