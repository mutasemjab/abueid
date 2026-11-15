{{-- One input per supported language. "name" is a dot path; "values" is ['ar' => …, 'en' => …]. --}}
@props(['name', 'label', 'values' => [], 'type' => 'text', 'required' => false, 'rows' => 3, 'hint' => null])

<div class="row g-3">
    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $meta)
        @php
            $path = "{$name}.{$locale}";
            $id = 'f_'.str_replace(['.', '*'], '_', $path);
            $value = old($path, $values[$locale] ?? null);
        @endphp
        <div class="col-md-6">
            <label class="form-label" for="{{ $id }}">
                {{ $label }} <span class="text-muted small">({{ $meta['native'] }})</span>
                @if ($required)<span class="text-danger">*</span>@endif
            </label>
            @if ($type === 'textarea')
                <textarea id="{{ $id }}" name="{{ input_name($path) }}" rows="{{ $rows }}" dir="{{ is_rtl($locale) ? 'rtl' : 'ltr' }}"
                          class="form-control @error($path) is-invalid @enderror" @if ($required) required @endif>{{ $value }}</textarea>
            @else
                <input id="{{ $id }}" type="text" name="{{ input_name($path) }}" value="{{ $value }}" dir="{{ is_rtl($locale) ? 'rtl' : 'ltr' }}"
                       class="form-control @error($path) is-invalid @enderror" @if ($required) required @endif>
            @endif
            @error($path)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
    @if ($hint)
        <div class="col-12 mt-1"><div class="form-text">{{ $hint }}</div></div>
    @endif
</div>
