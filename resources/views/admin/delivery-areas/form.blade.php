@extends('admin.layouts.app')
@section('title', $area->exists ? __('admin.areas.edit') : __('admin.areas.create'))

@section('content')
    <x-admin.page-header :title="$area->exists ? __('admin.areas.edit') : __('admin.areas.create')">
        <a href="{{ route('admin.delivery-areas.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <form method="POST"
          action="{{ $area->exists ? route('admin.delivery-areas.update', $area) : route('admin.delivery-areas.store') }}">
        @csrf
        @if ($area->exists) @method('PUT') @endif

        <x-admin.card :title="__('admin.details')" icon="bi-truck">
            <div class="d-flex flex-column gap-3">
                <x-admin.translatable name="name" :label="__('admin.fields.name')" :values="$area->getTranslations('name')" required />
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-admin.input name="fee" type="number" step="0.01" min="0" :label="__('admin.fields.fee')" :value="$area->fee" required ltr />
                    </div>
                </div>
                <x-admin.publishing :sort-order="$area->sort_order ?? 0" :active="$area->is_active ?? true" />
            </div>
        </x-admin.card>

        <x-admin.form-actions :cancel="route('admin.delivery-areas.index')" />
    </form>
@endsection
