@extends('admin.layouts.app')
@section('title', $category->exists ? __('admin.categories.edit') : __('admin.categories.create'))

@section('content')
    <x-admin.page-header :title="$category->exists ? __('admin.categories.edit') : __('admin.categories.create')">
        <a href="{{ route('admin.categories.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <form method="POST" enctype="multipart/form-data"
          action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <x-admin.card :title="__('admin.details')" icon="bi-grid" class="h-100">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.translatable name="name" :label="__('admin.fields.name')" :values="$category->getTranslations('name')" required />
                        <x-admin.translatable name="blurb" :label="__('admin.fields.blurb')" :values="$category->getTranslations('blurb')" />
                        <x-admin.translatable name="subcategories" type="textarea" rows="5" :label="__('admin.fields.subcategories')"
                                              :values="$category->getTranslations('subcategories')" :hint="__('admin.categories.subcategories_hint')" />
                    </div>
                </x-admin.card>
            </div>
            <div class="col-12 col-xl-4">
                <x-admin.card :title="__('admin.settings')" icon="bi-sliders" class="h-100">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.input name="slug" :label="__('admin.fields.slug')" :value="$category->slug" required ltr :hint="__('admin.slug_hint')" />
                        <x-admin.image name="image" :label="__('admin.fields.image')" :url="media_url($category->image)" remove-name="remove_image" />
                        <x-admin.publishing :sort-order="$category->sort_order ?? 0" :active="$category->is_active ?? true" />
                    </div>
                </x-admin.card>
            </div>
        </div>

        <x-admin.form-actions :cancel="route('admin.categories.index')" />
    </form>
@endsection
