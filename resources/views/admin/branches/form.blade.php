@extends('admin.layouts.app')
@section('title', $branch->exists ? __('admin.branches.edit') : __('admin.branches.create'))

@section('content')
    <x-admin.page-header :title="$branch->exists ? __('admin.branches.edit') : __('admin.branches.create')">
        <a href="{{ route('admin.branches.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <form method="POST" enctype="multipart/form-data"
          action="{{ $branch->exists ? route('admin.branches.update', $branch) : route('admin.branches.store') }}">
        @csrf
        @if ($branch->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <x-admin.card :title="__('admin.details')" icon="bi-geo-alt" class="h-100">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.translatable name="name" :label="__('admin.fields.name')" :values="$branch->getTranslations('name')" required />
                        <x-admin.translatable name="city" :label="__('admin.fields.city')" :values="$branch->getTranslations('city')" required />
                        <x-admin.translatable name="address" :label="__('admin.fields.address')" :values="$branch->getTranslations('address')" required />
                        <x-admin.translatable name="hours" :label="__('admin.fields.hours')" :values="$branch->getTranslations('hours')" />
                    </div>
                </x-admin.card>
            </div>
            <div class="col-12 col-xl-4">
                <x-admin.card :title="__('admin.settings')" icon="bi-sliders" class="h-100">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.input name="phone" :label="__('admin.fields.phone')" :value="$branch->phone" ltr />
                        <x-admin.input name="map_url" type="url" :label="__('admin.fields.map_url')" :value="$branch->map_url" ltr />
                        <x-admin.image name="photo" :label="__('admin.fields.photo')" :url="media_url($branch->photo)" remove-name="remove_photo" />
                        <div>
                            <input type="hidden" name="is_main" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="f_is_main" name="is_main" value="1"
                                       @checked(old('is_main', $branch->is_main))>
                                <label class="form-check-label" for="f_is_main">{{ __('admin.fields.is_main') }}</label>
                            </div>
                        </div>
                        <x-admin.publishing :sort-order="$branch->sort_order ?? 0" :active="$branch->is_active ?? true" />
                    </div>
                </x-admin.card>
            </div>
        </div>

        <x-admin.form-actions :cancel="route('admin.branches.index')" />
    </form>
@endsection
