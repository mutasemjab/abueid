{{-- One technical specification row. $index is numeric, or "__INDEX__" in the JS template. --}}
<div class="spec-row">
    <div class="d-flex justify-content-end mb-2">
        <button type="button" class="btn-icon-sm btn-delete" data-remove-spec title="{{ __('admin.delete') }}"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="d-flex flex-column gap-2">
        <x-admin.translatable :name="'specs.'.$index.'.label'" :label="__('admin.fields.spec_label')" :values="$spec['label'] ?? []" required />
        <x-admin.translatable :name="'specs.'.$index.'.value'" :label="__('admin.fields.spec_value')" :values="$spec['value'] ?? []" required />
    </div>
</div>
