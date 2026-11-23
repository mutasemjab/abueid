{{-- Renders a field described in config/sections.php or config/settings.php. --}}
@props(['name', 'field', 'content' => [], 'prefix' => 'content'])

@php
    $path = "{$prefix}.{$name}";
    $label = \App\Support\ContentSchema::label($name);
    $value = $content[$name] ?? null;
    $required = (bool) ($field['required'] ?? false);
    $inputType = match ($field['type']) {
        'number'   => 'number',
        'url'      => 'url',
        'email'    => 'email',
        'textarea' => 'textarea',
        default    => 'text',
    };
@endphp

@if ($field['type'] === 'image')
    <x-admin.image :name="$path" :label="$label" :url="media_url($value)" :remove-name="'remove.'.$name" />
@elseif ($field['translatable'] ?? false)
    <x-admin.translatable :name="$path" :label="$label" :values="(array) $value" :type="$field['type']" :required="$required" />
@else
    <x-admin.input :name="$path" :label="$label" :value="$value" :required="$required" :ltr="$field['ltr'] ?? false"
                   :type="$inputType" :step="$inputType === 'number' ? 'any' : null" />
@endif
