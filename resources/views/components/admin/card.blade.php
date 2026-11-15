@props(['title', 'icon' => 'bi-pencil-square'])

<div {{ $attributes->merge(['class' => 'panel-card']) }}>
    <div class="panel-card-header d-flex align-items-center justify-content-between gap-2">
        <h2 class="panel-card-title"><i class="bi {{ $icon }}"></i> {{ $title }}</h2>
        {{ $actions ?? '' }}
    </div>
    <div class="panel-card-body">
        {{ $slot }}
    </div>
</div>
