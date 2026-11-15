{{-- Sort order + active switch shared by every managed record. --}}
@props(['sortOrder' => 0, 'active' => true])

<div class="row g-3 align-items-end">
    <div class="col-sm-6">
        <x-admin.input name="sort_order" type="number" min="0" :label="__('admin.fields.sort_order')" :value="$sortOrder" :required="true" ltr />
    </div>
    <div class="col-sm-6">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" role="switch" id="f_is_active" name="is_active" value="1"
                   @checked(old('is_active', $active))>
            <label class="form-check-label" for="f_is_active">{{ __('admin.fields.is_active') }}</label>
        </div>
    </div>
</div>
