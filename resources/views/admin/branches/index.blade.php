@extends('admin.layouts.app')
@section('title', __('admin.branches.title'))

@section('content')
    <x-admin.page-header :title="__('admin.branches.title')" :subtitle="__('admin.branches.subtitle')">
        @can('branch-add')
            <a href="{{ route('admin.branches.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('admin.branches.create') }}</a>
        @endcan
    </x-admin.page-header>

    <x-admin.alerts />

    <div class="panel-card">
        <div class="panel-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.fields.name') }}</th>
                            <th>{{ __('admin.fields.city') }}</th>
                            <th>{{ __('admin.fields.phone') }}</th>
                            <th>{{ __('admin.fields.sort_order') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $branch)
                            <tr>
                                <td>{{ $branches->firstItem() + $loop->index }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $branch->name }}</span>
                                    @if ($branch->is_main)
                                        <span class="pill pill-info ms-1">{{ __('admin.fields.is_main') }}</span>
                                    @endif
                                </td>
                                <td>{{ $branch->city }}</td>
                                <td dir="ltr">{{ $branch->phone }}</td>
                                <td>{{ $branch->sort_order }}</td>
                                <td><x-admin.status :active="$branch->is_active" /></td>
                                <td>
                                    <x-admin.row-actions
                                        :edit="auth('admin')->user()->can('branch-edit') ? route('admin.branches.edit', $branch) : null"
                                        :destroy="auth('admin')->user()->can('branch-delete') ? route('admin.branches.destroy', $branch) : null" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="7" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($branches->hasPages())
            <div class="panel-card-body border-top pt-3">{{ $branches->links() }}</div>
        @endif
    </div>
@endsection
