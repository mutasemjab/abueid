@extends('admin.layouts.app')
@section('title', __('admin.products.title'))

@section('content')
    <x-admin.page-header :title="__('admin.products.title')" :subtitle="__('admin.products.subtitle')">
        @can('product-add')
            <a href="{{ route('admin.products.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('admin.products.create') }}</a>
        @endcan
    </x-admin.page-header>

    <x-admin.alerts />

    <x-admin.search :action="route('admin.products.index')" :placeholder="__('admin.products.search')">
        <div class="col-12 col-md-3">
            <select name="category" class="form-select form-select-sm no-select2">
                <option value="">{{ __('admin.all_categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
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
                            <th>{{ __('admin.fields.name') }}</th>
                            <th>{{ __('admin.fields.sku') }}</th>
                            <th>{{ __('admin.fields.category') }}</th>
                            <th>{{ __('admin.fields.price') }}</th>
                            <th>{{ __('admin.fields.stock') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="d-flex align-items-center gap-2">
                                    @if ($image = $product->mainImageUrl())
                                        <img src="{{ $image }}" alt="" class="admin-thumb">
                                    @endif
                                    <div>
                                        <span class="fw-semibold d-block">{{ $product->name }}</span>
                                        <small class="text-muted">{{ $product->brand?->name }}</small>
                                    </div>
                                </td>
                                <td dir="ltr"><code>{{ $product->sku }}</code></td>
                                <td>{{ $product->category?->name }}</td>
                                <td dir="ltr">{{ number_format($product->price, 2) }}</td>
                                <td>
                                    <span class="pill {{ $product->stock === 0 ? 'pill-danger' : ($product->stock <= 5 ? 'pill-warning' : 'pill-success') }}">{{ $product->stock }}</span>
                                </td>
                                <td><x-admin.status :active="$product->is_active" /></td>
                                <td>
                                    <x-admin.row-actions
                                        :edit="auth('admin')->user()->can('product-edit') ? route('admin.products.edit', $product) : null"
                                        :destroy="auth('admin')->user()->can('product-delete') ? route('admin.products.destroy', $product) : null" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="7" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($products->hasPages())
            <div class="panel-card-body border-top pt-3">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
