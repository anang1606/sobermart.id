@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open(['route' => ['bank.store'], 'files' => true]) !!}
    @method('POST')
    <div class="row">
        <div class="col-md-9">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/payment::payment.information') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Bank Code
                                </label>
                                <input required name="bank_code" type="text" class="form-control"
                                    placeholder="bca,bni,bri,...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Bank Name
                                </label>
                                <input required name="bank_name" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Bank Holder
                                </label>
                                <input required name="bank_holder" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    No Rekening
                                </label>
                                <input required name="bank_nomor" type="number" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Fee 
                                </label>
                                <input required name="fee" type="number" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    Bank Icon
                                </label>
                                <input required name="file" type="file" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 right-sidebar">
            @include('core/base::forms.partials.form-actions', [
                'title' => trans('plugins/payment::payment.action'),
            ])
        </div>
    </div>
    {!! Form::close() !!}
@stop
