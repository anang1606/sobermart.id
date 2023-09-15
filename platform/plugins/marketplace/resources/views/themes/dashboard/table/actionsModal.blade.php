<div class="table-actions">
    @if ($item->etalase !== '')
        @if (!empty($edit)  && $item->etalase !== '' && $item->etalase !== null)
            <a href="#" data-section="{{ $item->etalase }}" class="btn btn-icon btn-sm btn-primary editDialogCst"
                data-bs-toggle="tooltip" data-bs-original-title="{{ trans('core/base::tables.edit') }}"><i
                    class="fa fa-edit"></i></a>
        @endif

        @if (!empty($delete) && $item->etalase !== '' && $item->etalase !== null)
            <a href="#" class="btn btn-icon btn-sm btn-danger deleteDialogCst"
                data-section="{{ str_replace('?', '/', route($delete, $item->etalase)) }}" role="button"
                data-bs-toggle="tooltip" data-bs-original-title="{{ trans('core/base::tables.delete_entry') }}">
                <i class="fa fa-trash"></i>
            </a>
        @endif
    @endif
</div>
