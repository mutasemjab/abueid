@extends('admin.layouts.app')
@section('title', $section->title())

@section('content')
    <x-admin.page-header :title="$section->title()" :subtitle="$item->exists ? __('admin.sections.edit_item') : __('admin.sections.add_item')">
        <a href="{{ route('admin.sections.edit', $section) }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <form method="POST" enctype="multipart/form-data"
          action="{{ $item->exists ? route('admin.sections.items.update', [$section, $item]) : route('admin.sections.items.store', $section) }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <x-admin.card :title="__('admin.details')" :icon="$section->schema()['icon'] ?? 'bi-pencil-square'">
            <div class="d-flex flex-column gap-3">
                @foreach ($section->itemFields() as $name => $field)
                    <x-admin.schema-field :name="$name" :field="$field" :content="$item->content ?? []" />
                @endforeach
                <x-admin.publishing :sort-order="$item->sort_order" :active="$item->is_active" />
            </div>
        </x-admin.card>

        <x-admin.form-actions :cancel="route('admin.sections.edit', $section)" />
    </form>
@endsection
