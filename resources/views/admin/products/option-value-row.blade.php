{{-- One value of a product option: label per language + optional price change. --}}
@php($base = "options.{$optionIndex}.values.{$index}")

<div class="row g-2 align-items-start" data-option-value>
    @if (! empty($value['id']))
        <input type="hidden" name="{{ input_name($base.'.id') }}" value="{{ $value['id'] }}">
    @endif
    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $meta)
        @php($path = "{$base}.label.{$locale}")
        <div class="col-md-4">
            <input type="text" name="{{ input_name($path) }}" value="{{ old($path, $value['label'][$locale] ?? null) }}"
                   dir="{{ is_rtl($locale) ? 'rtl' : 'ltr' }}" placeholder="{{ __('admin.fields.option_value') }} ({{ $meta['native'] }})"
                   aria-label="{{ __('admin.fields.option_value') }} ({{ $meta['native'] }})"
                   class="form-control form-control-sm @error($path) is-invalid @enderror">
            @error($path)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
    <div class="col-md-3">
        @php($pricePath = "{$base}.price")
        <input type="number" step="0.01" name="{{ input_name($pricePath) }}" value="{{ old($pricePath, $value['price'] ?? null) }}" dir="ltr"
               placeholder="+0.00" aria-label="{{ __('admin.fields.option_price') }}"
               class="form-control form-control-sm @error($pricePath) is-invalid @enderror">
        @error($pricePath)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-1 d-flex">
        <button type="button" class="btn-icon-sm btn-delete" data-remove-option-value title="{{ __('admin.delete') }}"><i class="bi bi-x"></i></button>
    </div>
</div>
