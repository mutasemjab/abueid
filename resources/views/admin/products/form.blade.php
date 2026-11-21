@extends('admin.layouts.app')
@section('title', $product->exists ? __('admin.products.edit') : __('admin.products.create'))

@php
    $specs = old('specs', $product->specs?->map(fn ($spec) => [
        'label' => $spec->getTranslations('label'),
        'value' => $spec->getTranslations('value'),
    ])->all() ?? []);
@endphp

@section('content')
    <x-admin.page-header :title="$product->exists ? __('admin.products.edit') : __('admin.products.create')">
        <a href="{{ route('admin.products.index') }}" class="btn-outline-sm"><i class="bi bi-arrow-left-right"></i> {{ __('admin.back') }}</a>
    </x-admin.page-header>

    <x-admin.alerts />

    <form method="POST" enctype="multipart/form-data"
          action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8 d-flex flex-column gap-4">
                <x-admin.card :title="__('admin.details')" icon="bi-box-seam">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.translatable name="name" :label="__('admin.fields.name')" :values="$product->getTranslations('name')" required />
                        <x-admin.translatable name="short_description" :label="__('admin.fields.short_description')" :values="$product->getTranslations('short_description')" />
                        <x-admin.translatable name="subcategory" :label="__('admin.fields.subcategory')" :values="$product->getTranslations('subcategory')" />
                        <x-admin.translatable name="unit" :label="__('admin.fields.unit')" :values="$product->getTranslations('unit')" :hint="__('admin.products.unit_hint')" />
                    </div>
                </x-admin.card>

                <x-admin.card :title="__('admin.products.specs')" icon="bi-list-check">
                    <x-slot:actions>
                        <button type="button" class="btn-outline-sm" data-add-spec><i class="bi bi-plus"></i> {{ __('admin.products.add_spec') }}</button>
                    </x-slot:actions>
                    <p class="form-text mt-0">{{ __('admin.products.specs_hint') }}</p>
                    <div id="specRows">
                        @foreach ($specs as $index => $spec)
                            @include('admin.products.spec-row', ['index' => $index, 'spec' => $spec])
                        @endforeach
                    </div>
                    <template id="specTemplate">
                        @include('admin.products.spec-row', ['index' => '__INDEX__', 'spec' => []])
                    </template>
                </x-admin.card>

                <x-admin.card :title="__('admin.fields.images')" icon="bi-images">
                    @if ($product->images->isNotEmpty())
                        <div class="admin-gallery mb-3">
                            @foreach ($product->images as $image)
                                <label class="admin-gallery__item">
                                    <img src="{{ $image->url() }}" alt="" class="admin-thumb admin-thumb--lg">
                                    <span class="text-danger"><input type="checkbox" name="remove_images[]" value="{{ $image->id }}"> {{ __('admin.remove_image') }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    <x-admin.image name="images" :label="__('admin.products.add_images')" multiple />
                </x-admin.card>
            </div>

            <div class="col-12 col-xl-4 d-flex flex-column gap-4">
                <x-admin.card :title="__('admin.settings')" icon="bi-sliders">
                    <div class="d-flex flex-column gap-3">
                        <x-admin.input name="slug" :label="__('admin.fields.slug')" :value="$product->slug" required ltr :hint="__('admin.slug_hint')" />
                        <x-admin.input name="sku" :label="__('admin.fields.sku')" :value="$product->sku" required ltr />
                        <div>
                            <label class="form-label" for="f_category_id">{{ __('admin.fields.category') }} <span class="text-danger">*</span></label>
                            <select id="f_category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value=""></option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="f_brand_id">{{ __('admin.fields.brand') }}</label>
                            <select id="f_brand_id" name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                                <option value="">—</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected((int) old('brand_id', $product->brand_id) === $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <x-admin.input name="series" :label="__('admin.fields.series')" :value="$product->series" ltr />
                    </div>
                </x-admin.card>

                <x-admin.card :title="__('admin.products.pricing')" icon="bi-cash-coin">
                    <div class="row g-3">
                        <div class="col-6"><x-admin.input name="price" type="number" step="0.01" min="0" :label="__('admin.fields.price')" :value="$product->price" required ltr /></div>
                        <div class="col-6"><x-admin.input name="tax_rate" type="number" step="0.01" min="0" max="100" :label="__('admin.fields.tax_rate')" :value="$product->tax_rate" required ltr /></div>
                        <div class="col-12"><x-admin.input name="stock" type="number" min="0" :label="__('admin.fields.stock')" :value="$product->stock" ltr :hint="__('admin.products.stock_hint')" /></div>
                        <div class="col-6"><x-admin.input name="range_min" type="number" step="0.01" min="0" :label="__('admin.fields.range_min')" :value="$product->range_min" ltr /></div>
                        <div class="col-6"><x-admin.input name="range_max" type="number" step="0.01" min="0" :label="__('admin.fields.range_max')" :value="$product->range_max" ltr /></div>
                        <div class="col-12"><div class="form-text mt-0">{{ __('admin.products.range_hint') }}</div></div>
                    </div>
                </x-admin.card>

                <x-admin.card :title="__('admin.fields.pairs')" icon="bi-link-45deg">
                    {{-- Searches the catalogue as you type (see the script below); only the chosen products are rendered. --}}
                    <select name="pairs[]" id="f_pairs" class="form-select no-select2" multiple
                            data-url="{{ route('admin.products.search', ['except' => $product->id]) }}"
                            data-placeholder="{{ __('admin.products.pairs_hint') }}">
                        @foreach ($pairOptions as $option)
                            <option value="{{ $option->id }}" selected>{{ $option->sku }} — {{ $option->name }}</option>
                        @endforeach
                    </select>
                </x-admin.card>

                <x-admin.card :title="__('admin.visibility')" icon="bi-eye">
                    <x-admin.publishing :sort-order="$product->sort_order ?? 0" :active="$product->is_active ?? true" />
                </x-admin.card>
            </div>
        </div>

        <x-admin.form-actions :cancel="route('admin.products.index')" />
    </form>
@endsection

@push('scripts')
<script>
(() => {
    const rows = document.getElementById('specRows');
    const template = document.getElementById('specTemplate').innerHTML;
    let next = rows.children.length;

    document.querySelector('[data-add-spec]').addEventListener('click', () => {
        rows.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', next++));
    });

    rows.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove-spec]');
        if (button) button.closest('.spec-row').remove();
    });
})();

// "Pairs with": search the catalogue on the server instead of listing every product.
$(function () {
    const select = $('#f_pairs');
    select.select2({
        theme: 'bootstrap-5',
        dir: document.documentElement.getAttribute('dir') || 'ltr',
        width: '100%',
        placeholder: select.data('placeholder'),
        minimumInputLength: 2,
        ajax: { url: select.data('url'), dataType: 'json', delay: 250, data: (params) => ({ q: params.term }) },
    });
});

</script>
@endpush
