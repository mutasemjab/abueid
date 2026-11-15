@extends('admin.layouts.app')
@section('title', __('admin.areas.title'))

@section('content')
    <x-admin.page-header :title="__('admin.areas.title')" :subtitle="__('admin.areas.subtitle')">
        @can('area-add')
            <a href="{{ route('admin.delivery-areas.create') }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('admin.areas.create') }}</a>
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
                            <th>{{ __('admin.fields.fee') }}</th>
                            <th>{{ __('admin.fields.sort_order') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($areas as $area)
                            <tr>
                                <td>{{ $areas->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $area->name }}</td>
                                <td dir="ltr">{{ number_format($area->fee, 2) }}</td>
                                <td>{{ $area->sort_order }}</td>
                                <td><x-admin.status :active="$area->is_active" /></td>
                                <td>
                                    <x-admin.row-actions
                                        :edit="auth('admin')->user()->can('area-edit') ? route('admin.delivery-areas.edit', $area) : null"
                                        :destroy="auth('admin')->user()->can('area-delete') ? route('admin.delivery-areas.destroy', $area) : null" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="6" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($areas->hasPages())
            <div class="panel-card-body border-top pt-3">{{ $areas->links() }}</div>
        @endif
    </div>
@endsection
