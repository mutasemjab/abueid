@props(['active'])

<span class="pill {{ $active ? 'pill-success' : 'pill-neutral' }}">{{ $active ? __('admin.active') : __('admin.inactive') }}</span>
