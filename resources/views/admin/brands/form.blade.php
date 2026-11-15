@extends('admin.layouts.app')
@section('title', $brand->exists ? __('admin.brands.edit') : __('admin.brands.create'))

@section('content')
    <x-admin.page-header :title="$brand->exists ? __('admin.brands.edit') : __('admin.brands.create')">
        <a href="{{ route('admin.brands.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <form method="POST" enctype="multipart/form-data"
          action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}">
        @csrf
        @if ($brand->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <x-admin.card :title="__('admin.details')" icon="bi-award" class="h-100">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.input name="name" :label="__('admin.fields.name')" :value="$brand->name" required :hint="__('admin.brands.name_hint')" />
                        <x-admin.translatable name="origin" :label="__('admin.fields.origin')" :values="$brand->getTranslations('origin')" />
                        <x-admin.translatable name="product_line" :label="__('admin.fields.product_line')" :values="$brand->getTranslations('product_line')" />
                    </div>
                </x-admin.card>
            </div>
            <div class="col-12 col-xl-4">
                <x-admin.card :title="__('admin.settings')" icon="bi-sliders" class="h-100">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.input name="slug" :label="__('admin.fields.slug')" :value="$brand->slug" required ltr :hint="__('admin.slug_hint')" />
                        <x-admin.image name="logo" :label="__('admin.fields.logo')" :url="media_url($brand->logo)" remove-name="remove_logo" />
                        <x-admin.publishing :sort-order="$brand->sort_order ?? 0" :active="$brand->is_active ?? true" />
                    </div>
                </x-admin.card>
            </div>
        </div>

        <x-admin.form-actions :cancel="route('admin.brands.index')" />
    </form>
@endsection
