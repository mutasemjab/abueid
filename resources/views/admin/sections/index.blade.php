@extends('admin.layouts.app')
@section('title', __('admin.sections.title'))

@section('content')
    <x-admin.page-header :title="__('admin.sections.title')" :subtitle="__('admin.sections.subtitle')" />

    <x-admin.alerts />

    <div class="panel-card">
        <div class="panel-card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.sections.section') }}</th>
                            <th>{{ __('admin.sections.placement') }}</th>
                            <th>{{ __('admin.fields.sort_order') }}</th>
                            <th>{{ __('admin.sections.in_menu') }}</th>
                            <th>{{ __('admin.sections.items') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sections as $section)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi {{ $section->schema()['icon'] ?? 'bi-square' }} text-muted"></i>
                                        <div>
                                            <span class="fw-semibold d-block">{{ $section->title() }}</span>
                                            <small class="text-muted">{{ __("sections.descriptions.{$section->key}") }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="pill pill-neutral">{{ $section->isOnHomePage() ? __('admin.sections.home_page') : __('admin.sections.layout') }}</span>
                                </td>
                                <td>{{ $section->isOnHomePage() ? $section->sort_order : '—' }}</td>
                                <td>
                                    @if ($section->canBeInMenu())
                                        <i class="bi {{ $section->show_in_menu ? 'bi-check-circle-fill text-success' : 'bi-dash-circle text-muted' }}"></i>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $section->hasItems() ? $section->items_count : '—' }}</td>
                                <td><x-admin.status :active="$section->is_active" /></td>
                                <td>
                                    <x-admin.row-actions :edit="auth('admin')->user()->can('section-edit') ? route('admin.sections.edit', $section) : null" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
