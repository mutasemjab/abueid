@extends('admin.layouts.app')
@section('title', $section->title())

@php($itemFields = $section->itemFields())

@section('content')
    <x-admin.page-header :title="$section->title()" :subtitle="__('sections.descriptions.'.$section->key)">
        <a href="{{ route('admin.sections.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.sections.update', $section) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <x-admin.card :title="__('admin.sections.content')" :icon="$section->schema()['icon'] ?? 'bi-pencil-square'" class="h-100">
                    @if ($section->fields())
                        <div class="d-flex flex-column gap-3">
                            @foreach ($section->fields() as $name => $field)
                                <x-admin.schema-field :name="$name" :field="$field" :content="$section->content ?? []" />
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('admin.sections.items_only') }}</p>
                    @endif
                </x-admin.card>
            </div>

            <div class="col-12 col-xl-4">
                <x-admin.card :title="__('admin.visibility')" icon="bi-eye" class="h-100">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.publishing :sort-order="$section->sort_order" :active="$section->is_active" />
                        @if ($section->canBeInMenu())
                            <div class="form-check form-switch">
                                <input type="hidden" name="show_in_menu" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="f_show_in_menu" name="show_in_menu" value="1"
                                       @checked(old('show_in_menu', $section->show_in_menu))>
                                <label class="form-check-label" for="f_show_in_menu">{{ __('admin.sections.show_in_menu') }}</label>
                            </div>
                        @endif
                        @if ($section->isOnHomePage())
                            <p class="form-text mb-0">{{ __('admin.sections.order_hint') }}</p>
                        @endif
                    </div>
                </x-admin.card>
            </div>
        </div>

        <x-admin.form-actions :cancel="route('admin.sections.index')" />
    </form>

    @if ($section->hasItems())
        @php($previewField = array_key_first(array_filter($itemFields, fn ($field) => $field['type'] !== 'image')))
        @php($imageField = array_key_first(array_filter($itemFields, fn ($field) => $field['type'] === 'image')))

        <x-admin.card :title="__('admin.sections.items')" icon="bi-collection" class="mb-4">
            <x-slot:actions>
                <a href="{{ route('admin.sections.items.create', $section) }}" class="btn-primary-sm"><i class="bi bi-plus-circle"></i> {{ __('admin.sections.add_item') }}</a>
            </x-slot:actions>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ \App\Support\ContentSchema::label($previewField) }}</th>
                            <th>{{ __('admin.fields.sort_order') }}</th>
                            <th>{{ __('admin.fields.status') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($section->items as $item)
                            <tr>
                                <td class="d-flex align-items-center gap-2">
                                    @if ($imageField && ($image = $item->imageUrl($imageField)))
                                        <img src="{{ $image }}" alt="" class="admin-thumb">
                                    @endif
                                    <span class="fw-semibold">{{ $item->value($previewField) }}</span>
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td><x-admin.status :active="$item->is_active" /></td>
                                <td>
                                    <x-admin.row-actions
                                        :edit="route('admin.sections.items.edit', [$section, $item])"
                                        :destroy="route('admin.sections.items.destroy', [$section, $item])" />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty-row colspan="4" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    @endif
@endsection
