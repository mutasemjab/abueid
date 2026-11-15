@extends('admin.layouts.app')
@section('title', __('admin.settings_page.title'))

@section('content')
    <x-admin.page-header :title="__('admin.settings_page.title')" :subtitle="__('admin.settings_page.subtitle')" />

    <x-admin.alerts />

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="d-flex flex-column gap-4">
            @foreach ($groups as $group => $definition)
                <x-admin.card :title="__('sections.settings_groups.'.$group)" :icon="$definition['icon']">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($definition['fields'] as $name => $field)
                            <x-admin.schema-field :name="$name" :field="$field" :content="$values" prefix="settings" />
                        @endforeach
                    </div>
                </x-admin.card>
            @endforeach
        </div>

        <x-admin.form-actions :cancel="route('admin.dashboard')" />
    </form>
@endsection
