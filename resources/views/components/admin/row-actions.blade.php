{{-- Edit / delete buttons of a table row. Pass null to hide a button. --}}
@props(['edit' => null, 'destroy' => null, 'show' => null])

<div class="d-flex gap-1">
    @if ($show)
        <a href="{{ $show }}" class="btn-icon-sm btn-view" title="{{ __('admin.view') }}"><i class="bi bi-eye"></i></a>
    @endif
    @if ($edit)
        <a href="{{ $edit }}" class="btn-icon-sm btn-edit" title="{{ __('admin.edit') }}"><i class="bi bi-pencil"></i></a>
    @endif
    @if ($destroy)
        <form action="{{ $destroy }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('admin.confirm_delete')))">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-icon-sm btn-delete" title="{{ __('admin.delete') }}"><i class="bi bi-trash"></i></button>
        </form>
    @endif
</div>
