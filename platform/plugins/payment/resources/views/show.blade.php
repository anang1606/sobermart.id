@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $payment) @endphp
    {!! Form::open(['route' => ['payment.update', $payment->id]]) !!}
    @method('PUT')
    <div class="row">
        <div class="col-md-9">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <span>{{ trans('plugins/payment::payment.information') }}</span>
                    </h4>
                </div>
                <div class="widget-body">
                    <p>{{ trans('plugins/payment::payment.created_at') }}: <strong>{{ $payment->created_at }}</strong></p>
                    <p>{{ trans('plugins/payment::payment.payment_channel') }}:
                        <strong>{{ $payment->payment_channel->label() }}</strong>
                    </p>
                    <p>Sub Amount: <strong>{{ $payment->subAmount }}
                            {{ $payment->currency }}</strong></p>
                    @if ($payment->totalShipmentJne > 0)
                    <p>JNE Amount: <strong>{{ $payment->totalShipmentJne }}
                            {{ $payment->currency }}</strong></p>
                    @endif
                    @if ($payment->totalShipmentJnt > 0)
                    <p>J&T Amount: <strong>{{ $payment->totalShipmentJnt }}
                            {{ $payment->currency }}</strong></p>
                    @endif
                    <p>Kode Unik: <strong>{{ $payment->amount - ($payment->subAmount + $payment->totalShipmentJne + $payment->totalShipmentJnt) }}
                        {{ $payment->currency }}</strong></p>
                    <p>{{ trans('plugins/payment::payment.total') }}: <strong>{{ $payment->amount }}
                            {{ $payment->currency }}</strong></p>
                    <p>{{ trans('plugins/payment::payment.status') }}: <strong>{!! $payment->status->label() !!}</strong></p>
                    @if ($payment->customer_id && $payment->customer && $payment->customer_type && class_exists($payment->customer_type))
                        <p>{{ trans('plugins/payment::payment.payer_name') }}:
                            <strong>{{ $payment->customer->name }}</strong>
                        </p>
                        <p>{{ trans('plugins/payment::payment.email') }}: <strong>{{ $payment->customer->email }}</strong>
                        </p>
                        @if ($payment->customer->phone)
                            <p>{{ trans('plugins/payment::payment.phone') }}:
                                <strong>{{ $payment->customer->phone }}</strong>
                            </p>
                        @endif
                    @endif

                    @if ($payment->type_status !== 'paket')
                        {!! $detail !!}
                    @endif
                    @if (count($payment_verifs) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Nomor Rekening</th>
                                        <th>Nama Pengirim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment_verifs as $payment_verif)
                                        <tr>
                                            <td>
                                                <a href="{{ RvMedia::url($payment_verif->image) }}" target="_blank"
                                                    rel="noopener noreferrer">
                                                    <img src="{{ RvMedia::url($payment_verif->image) }}"
                                                        style="width: 150px;object-fit: cover;" alt="" />
                                                </a>
                                            </td>
                                            <td>
                                                {{ $payment_verif->bank_number }}
                                            </td>
                                            <td>
                                                {{ $payment_verif->bank_holder }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            @php do_action(BASE_ACTION_META_BOXES, 'advanced', $payment) @endphp
        </div>
        <div class="col-md-3 right-sidebar">
            @include('core/base::forms.partials.form-actions', [
                'title' => trans('plugins/payment::payment.action'),
            ])
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4><label for="status"
                            class="control-label required">{{ trans('core/base::tables.status') }}</label>
                    </h4>
                </div>
                <div class="widget-body">
                    {!! Form::customSelect('status', $paymentStatuses, $payment->status) !!}
                </div>
            </div>
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>
                        <label for="notes" class="control-label">
                            Notes/Catatan
                        </label>
                    </h4>
                </div>
                <div class="widget-body">
                    <textarea name="notes" class="form-control" rows="5">{{ $payment->notes }}</textarea>
                </div>
            </div>
            @php do_action(BASE_ACTION_META_BOXES, 'side', $payment) @endphp
        </div>
    </div>
    {!! Form::close() !!}
@stop
