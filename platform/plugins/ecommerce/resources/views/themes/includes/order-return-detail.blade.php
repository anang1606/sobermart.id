@if ($orderReturn)
    <div class="customer-order-detail">
        <div class="row">
            <div class="col-md-6">
                <h5>{{ __('Return Product(s) Information') }}</h5>
                <p>
                    <span>{{ __('Request number') }}: </span>
                    <strong>{{ $orderReturn->code }}</strong>
                </p>
                <p>
                    <span>{{ __('Order Id') }}: </span>
                    <strong>{{ $orderReturn->order->code }}</strong>
                </p>
            </div>
            <div class="col-md-6">
                <p>
                    <span>{{ __('Time') }}: </span>
                    <strong class="text-info">{{ $orderReturn->created_at->translatedFormat('h:m d/m/Y') }}</strong>
                </p>
                <p>
                    <span>{{ __('Status') }}: </span>
                    <strong class="text-warning">{{ $orderReturn->return_status->label() }}</strong>
                </p>
                @if (!EcommerceHelper::allowPartialReturn())
                    <p>
                        <span>{{ __('Reason') }}: </span>
                        <strong class="text-warning">{{ $orderReturn->reason->label() }}</strong>
                    </p>
                @endif
            </div>

        </div>
        <br/>
        <h5>{{ __('Return items') }}</h5>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">{{ __('Image') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Quantity') }}</th>
                            @if (EcommerceHelper::allowPartialReturn())
                                <th class="text-end">{{ __('Reason') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($orderReturn->items as $key => $item)
                            @php
                                $product = get_products([
                                    'condition' => [
                                        'ec_products.id' => $item->product_id,
                                    ],
                                    'take'   => 1,
                                    'select' => [
                                        'ec_products.id',
                                        'ec_products.images',
                                        'ec_products.name',
                                        'ec_products.price',
                                        'ec_products.sale_price',
                                        'ec_products.sale_type',
                                        'ec_products.start_date',
                                        'ec_products.end_date',
                                        'ec_products.sku',
                                        'ec_products.is_variation',
                                        'ec_products.status',
                                        'ec_products.order',
                                        'ec_products.created_at',
                                    ],
                                ]);

                            @endphp
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-center">
                                    <img src="{{ RvMedia::getImageUrl($item->product_image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                         alt="{{ $item->product_name }}" width="50">
                                </td>
                                <td>
                                    {{ $item->product_name }} @if ($product && $product->sku)
                                        ({{ $product->sku }})
                                    @endif

                                    @if ($product && $product->is_variation)
                                        <p>
                                            <small>{{ $product->variation_attributes }}</small>
                                        </p>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-info">{{ number_format($item->qty) }}</strong>
                                </td>
                                @if (EcommerceHelper::allowPartialReturn())
                                    <td class="text-end">
                                        <span class="text-warning">{{ $item->reason->label() }}</span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                @if ($orderReturn->return_status->label() === 'Processing')
                    <a href="{{ route('customer.returns.confirm', $orderReturn->id) }}"
                        onclick="return confirm('{{ __('Are you sure?') }}')"
                        class="btn btn-lg btn-primary">
                        {{ __('Confirm return order') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
@else
    <p class="text-center text-danger">{{ __('Order Return Request not found!') }}</p>
@endif
