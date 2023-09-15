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
                        {!! $dataTable->table(compact('id', 'class'), false) !!}
                    @show
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-confirm-edit-reviews" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title"><i class="til_img"></i><strong>Reply Review</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            {!! Form::open([
                'route' => 'marketplace.vendor.reviews.reply',
                'method' => 'POST',
                'files' => true,
            ]) !!}
            <div class="modal-body with-padding">
                <div class="alert alert-secondary" id="comment-user" role="alert">
                </div>
                <div class="form-group mt-3">
                    <label>Reply Review</label>
                    <input name="id_comment" type="hidden" readonly id="id_comment" class="form-control">
                    <textarea name="comment" id="txt-comment" required="required" aria-required="true" rows="8"
                        placeholder="Write your reply" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="float-start btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="float-end btn btn-success btn-submit">Submit</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@include('core/table::modal')
@stop
@push('scripts')
{!! $dataTable->scripts() !!}
<script>
    $(document).on('click', '.replyDialog', function() {
        $('.modal-confirm-edit-reviews').modal('show')
        $('.modal-confirm-edit-reviews #id_comment').val(atob($(this).data('comment')))
        $('.modal-confirm-edit-reviews #comment-user').text(atob($(this).data('reviews')))
    })
</script>
@endpush
