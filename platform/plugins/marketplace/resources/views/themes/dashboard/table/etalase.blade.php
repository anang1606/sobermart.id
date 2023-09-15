@extends(MarketplaceHelper::viewPath('dashboard.layouts.master'))
@section('content')
    <div class="container page-content" style="background: none; max-width: none">
        <div class="table-wrapper">
            @if ($table->isHasFilter())
                <div class="table-configuration-wrap" @if (request()->has('filter_table_id')) style="display: block;" @endif>
                    <span class="configuration-close-btn btn-show-table-options"><i class="fa fa-times"></i></span>
                    {!! $table->renderFilter() !!}
                </div>
            @endif
            <div class="portlet light bordered portlet-no-padding">
                <div class="portlet-title">
                    <div class="caption">
                        <div class="wrapper-action">
                            @if ($actions)
                                <div class="btn-group">
                                    <a class="btn btn-secondary dropdown-toggle" href="#"
                                        data-bs-toggle="dropdown">{{ trans('core/table::table.bulk_actions') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        @foreach ($actions as $action)
                                            <li>
                                                {!! $action !!}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($table->isHasFilter())
                                <button
                                    class="btn btn-primary btn-show-table-options">{{ trans('core/table::table.filters') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-responsive @if ($actions) table-has-actions @endif @if ($table->isHasFilter()) table-has-filter @endif"
                        style="overflow-x: inherit">
                    @section('main-table')
                        {!! $dataTable->table(['data-table' => 'collapse-table'], false) !!}
                        {{--  {!! $dataTable->table(compact('id', 'class'), false) !!}  --}}
                    @show
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-confirm-delete-etalase" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title"><i class="til_img"></i><strong>Confirm delete</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>

            <div class="modal-body with-padding">
                <div>Do you really want to delete this record?</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="float-start btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                <button class="float-end btn btn-danger delete-crud-entry-etalase">Delete</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-confirm-edit-etalase" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title"><i class="til_img"></i><strong>Edit Etalase</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            {{ Form::open(['route' => ['marketplace.vendor.etalase.update'], 'method' => 'POST']) }}
            <div class="modal-body with-padding">
                <div class="form-group">
                    <label>Nama Etalase</label>
                    <input name="old_etalase" type="hidden" readonly id="old_etalase" class="form-control">
                    <input type="text" name="name_etalase" id="etalase_name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="float-start btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="float-end btn btn-success">Update</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@push('scripts')
{!! $dataTable->scripts() !!}
<script>
    var delete_url = '';
    $(document).on('click', '.deleteDialogCst', function(e) {
        delete_url = $(this).data('section');
        $('.modal-confirm-delete-etalase').modal('show')
    });
    $(document).on('click', '.editDialogCst', function(e) {
        $('.modal-confirm-edit-etalase').modal('show')
        $('.modal-confirm-edit-etalase #old_etalase').val($(this).data('section'))
        $('.modal-confirm-edit-etalase #etalase_name').val($(this).data('section'))
    });
    $(document).on('click', '.delete-crud-entry-etalase', function(e) {
        $.ajax({
            url: delete_url,
            type: 'POST',
            success: function(data) {
                $('.modal-confirm-delete-etalase').modal('hide')
                if (data.error) {
                    toastr.success(data.message, 'Error');
                } else {
                    toastr.success(data.message, 'Success');
                    $('.buttons-reload').click();
                }
            }
        })
    });
</script>
@endpush
