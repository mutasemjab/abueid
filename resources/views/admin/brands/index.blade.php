@extends('admin.layouts.app')
@section('title', __('admin.brands.title'))

@section('content')
    <x-admin.page-header :title="__('admin.brands.title')" :subtitle="__('admin.brands.subtitle')">
        @can('brand-add')
            <a href="{{ route('admin.brands.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('admin.brands.create') }}</a>
        @endcan
    </x-admin.page-header>

    <x-admin.alerts />
    <x-admin.search :action="route('admin.brands.index')" />

    <div class="panel-card">
        <div class="panel-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.fields.name') }}</th>
                            <th>{{ __('admin.fields.origin') }}</th>
                            <th>{{ __('admin.products.title') }}</th>
                            <th>{{ __('admin.fields.sort_order') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr>
                                <td>{{ $brands->firstItem() + $loop->index }}</td>
                                <td class="d-flex align-items-center gap-2">
                                    @if ($brand->logo)
                                        <img src="{{ media_url($brand->logo) }}" alt="" class="admin-thumb">
                                    @endif
                                    <span class="fw-semibold">{{ $brand->name }}</span>
                                </td>
                                <td>{{ $brand->origin }}</td>
                                <td>{{ $brand->products_count }}</td>
                                <td>{{ $brand->sort_order }}</td>
                                <td><x-admin.status :active="$brand->is_active" /></td>
                                <td>
                                    <x-admin.row-actions
                                        :edit="auth('admin')->user()->can('brand-edit') ? route('admin.brands.edit', $brand) : null"
                                        :destroy="auth('admin')->user()->can('brand-delete') ? route('admin.brands.destroy', $brand) : null" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="7" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($brands->hasPages())
            <div class="panel-card-body border-top pt-3">{{ $brands->links() }}</div>
        @endif
    </div>
@endsection
