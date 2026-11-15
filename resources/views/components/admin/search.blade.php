{{-- GET filter bar. Extra filters go in the slot. --}}
@props(['action', 'placeholder' => null])

<div class="panel-card mb-3">
    <div class="panel-card-body">
        <form method="GET" action="{{ $action }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                       placeholder="{{ $placeholder ?? __('admin.search') }}">
            </div>
            {{ $slot }}
            <div class="col-auto">
                <button type="submit" class="btn-primary-sm"><i class="bi bi-search"></i></button>
            </div>
            @if (request()->hasAny(['search', 'category', 'status']))
                <div class="col-auto">
                    <a href="{{ $action }}" class="btn-outline-sm"><i class="bi bi-x"></i> {{ __('admin.clear') }}</a>
                </div>
            @endif
        </form>
    </div>
</div>
