@props(['cancel'])

<div class="d-flex gap-2 mt-4 pb-4">
    <button type="submit" class="btn-primary-sm"><i class="bi bi-save"></i> {{ __('admin.save') }}</button>
    <a href="{{ $cancel }}" class="btn-outline-sm">{{ __('admin.cancel') }}</a>
</div>
