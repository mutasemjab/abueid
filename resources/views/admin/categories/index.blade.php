@extends('admin.layouts.app')
@section('title', __('admin.categories.title'))

@section('content')
    <x-admin.page-header :title="__('admin.categories.title')" :subtitle="__('admin.categories.subtitle')">
        @can('category-add')
            <a href="{{ route('admin.categories.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('admin.categories.create') }}</a>
        @endcan
    </x-admin.page-header>

    <x-admin.alerts />
    <x-admin.search :action="route('admin.categories.index')" />

    <div class="panel-card">
        <div class="panel-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.fields.name') }}</th>
                            <th>{{ __('admin.fields.slug') }}</th>
                            <th>{{ __('admin.products.title') }}</th>
                            <th>{{ __('admin.fields.sort_order') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $categories->firstItem() + $loop->index }}</td>
                                <td class="d-flex align-items-center gap-2">
                                    @if ($category->image)
                                        <img src="{{ media_url($category->image) }}" alt="" class="admin-thumb">
                                    @endif
                                    <span class="fw-semibold">{{ $category->name }}</span>
                                </td>
                                <td dir="ltr"><code>{{ $category->slug }}</code></td>
                                <td>{{ $category->products_count }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td><x-admin.status :active="$category->is_active" /></td>
                                <td>
                                    <x-admin.row-actions
                                        :edit="auth('admin')->user()->can('category-edit') ? route('admin.categories.edit', $category) : null"
                                        :destroy="auth('admin')->user()->can('category-delete') ? route('admin.categories.destroy', $category) : null" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="7" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($categories->hasPages())
            <div class="panel-card-body border-top pt-3">{{ $categories->links() }}</div>
        @endif
    </div>
@endsection
