@props(['title', 'subtitle' => null])

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-sub">{{ $subtitle }}</p>
        @endif
    </div>
    @if (! $slot->isEmpty())
        <div class="d-flex gap-2 flex-wrap">{{ $slot }}</div>
    @endif
</div>
