{{-- One product option (e.g. "Amperage") with its values. $index is numeric, or "__OPTION__" in the JS template. --}}
<div class="spec-row option-row" data-option-index="{{ $index }}">
    @if (! empty($option['id']))
        <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option['id'] }}">
    @endif
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold small text-muted"><i class="bi bi-ui-radios"></i> {{ __('admin.fields.option_name') }}</span>
        <button type="button" class="btn-icon-sm btn-delete" data-remove-option title="{{ __('admin.delete') }}"><i class="bi bi-x-lg"></i></button>
    </div>

    <x-admin.translatable :name="'options.'.$index.'.name'" :label="__('admin.fields.option_name')" :values="$option['name'] ?? []" required />

    <div class="mt-3">
        <div class="row g-2 small text-muted fw-semibold d-none d-md-flex">
            @foreach (LaravelLocalization::getSupportedLocales() as $meta)
                <div class="col-md-4">{{ __('admin.fields.option_value') }} ({{ $meta['native'] }})</div>
            @endforeach
            <div class="col-md-3">{{ __('admin.fields.option_price') }}</div>
        </div>
        <div class="d-flex flex-column gap-2 mt-1" data-option-values>
            @foreach ($option['values'] ?? [] as $valueIndex => $value)
                @include('admin.products.option-value-row', ['optionIndex' => $index, 'index' => $valueIndex, 'value' => $value])
            @endforeach
        </div>
        @error("options.{$index}.values")<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <button type="button" class="btn-outline-sm mt-2" data-add-option-value><i class="bi bi-plus"></i> {{ __('admin.products.add_option_value') }}</button>
    </div>
</div>
