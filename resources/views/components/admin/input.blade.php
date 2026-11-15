{{-- Single (non-translatable) input. "name" is a dot path, e.g. "price" or "content.value". --}}
@props(['name', 'label', 'value' => null, 'type' => 'text', 'required' => false, 'ltr' => false, 'hint' => null])

@php($id = 'f_'.str_replace('.', '_', $name))

<div>
    <label class="form-label" for="{{ $id }}">
        {{ $label }} @if ($required)<span class="text-danger">*</span>@endif
    </label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ input_name($name) }}" rows="3"
                  {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
                  @if ($ltr) dir="ltr" @endif @if ($required) required @endif>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ input_name($name) }}" value="{{ old($name, $value) }}"
               {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
               @if ($ltr) dir="ltr" @endif @if ($required) required @endif>
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($hint)<div class="form-text">{{ $hint }}</div>@endif
</div>
