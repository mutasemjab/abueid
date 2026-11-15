{{-- Image upload with preview of the current image and an optional "remove" checkbox. --}}
@props(['name', 'label', 'url' => null, 'removeName' => null, 'multiple' => false])

@php($id = 'f_'.str_replace('.', '_', $name))

<div>
    <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    @if ($url)
        <div class="d-flex align-items-center gap-3 mb-2">
            <img src="{{ $url }}" alt="" class="admin-thumb admin-thumb--lg">
            @if ($removeName)
                <label class="d-flex align-items-center gap-1 small text-danger">
                    <input type="checkbox" name="{{ input_name($removeName) }}" value="1"> {{ __('admin.remove_image') }}
                </label>
            @endif
        </div>
    @endif
    <input id="{{ $id }}" type="file" accept="image/*"
           name="{{ input_name($name) }}{{ $multiple ? '[]' : '' }}" @if ($multiple) multiple @endif
           class="form-control @error($name) is-invalid @enderror @error($name.'.*') is-invalid @enderror">
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">{{ __('admin.image_hint') }}</div>
</div>
