@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
<div class="row">
    <div class="col-md-12 right-sidebar">
        <div class="widget meta-boxes">
            <div class="widget-title">
                <h4><label for="status" class="control-label" aria-required="true">List belanja</label></h4>
            </div>
            <div class="widget-body">
                <table class="table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="20px">No.</th>
                            <th class="text-center">{{ __('Image') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th class="text-center">{{ __('Amount') }}</th>
                            <th class="text-end" style="width: 100px">{{ __('Quantity') }}</th>
                            <th class="price text-end">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no =1;
                        @endphp
                        @foreach ($orders as $order)
                            @foreach ($order->products as $key => $orderProduct)
                            @php
                                $product = get_products([
                                    'condition' => [
                                        'ec_products.id' => $orderProduct->product_id,
                                    ],
                                    'take' => 1,
                                    'select' => ['ec_products.id', 'ec_products.images', 'ec_products.name', 'ec_products.price', 'ec_products.sale_price', 'ec_products.sale_type', 'ec_products.start_date', 'ec_products.end_date', 'ec_products.sku', 'ec_products.is_variation', 'ec_products.status', 'ec_products.order', 'ec_products.created_at'],
                                    'include_out_of_stock_products' => true,
                                ]);
                            @endphp

                            <tr>
                                <td>
                                    {{$no++}}
                                </td>
                                <td class="text-center">
                                    <img src="{{ RvMedia::getImageUrl($orderProduct->product_image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                        width="50" alt="{{ $orderProduct->product_name }}">
                                </td>
                                <td>
                                    @if ($product)
                                        {{ $product->original_product->name }} @if ($product->sku)
                                            ({{ $product->sku }})
                                        @endif
                                        @if ($product->is_variation)
                                            <p class="mb-0">
                                                <small>
                                                    @php $attributes = get_product_attributes($product->id) @endphp
                                                    @if (!empty($attributes))
                                                        @foreach ($attributes as $attribute)
                                                            {{ $attribute->attribute_set_title }}
                                                            : {{ $attribute->title }}@if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </small>
                                            </p>
                                        @endif
                                    @else
                                        {{ $orderProduct->product_name }}
                                    @endif

                                    @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                                        @foreach ($orderProduct->options as $option)
                                            @if (!empty($option['key']) && !empty($option['value']))
                                                <p class="mb-0"><small>{{ $option['key'] }}:
                                                        <strong> {{ $option['value'] }}</strong></small></p>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if (!empty($orderProduct->product_options) && is_array($orderProduct->product_options))
                                        {!! render_product_options_info($orderProduct->product_options, $product, true) !!}
                                    @endif
                                </td>
                                <td>{{ format_price($orderProduct->price) }}</td>
                                <td class="text-center">{{ $orderProduct->qty }}</td>
                                <td class="money text-end">
                                    <strong>
                                        {{ format_price($orderProduct->price * $orderProduct->qty) }}
                                    </strong>
                                </td>
                            </tr>
                        @endforeach
                        @endforeach
                        {{-- @foreach ($orders as $order)
                            <tr>
                                <td>
                                    {{$no++}}
                                </td>
                                <td>
                                    {{format_price($order->amount)}}
                                </td>
                                <td>
                                    {{format_price($order->shipping_amount)}}
                                </td>
                                <td>
                                    {{BaseHelper::clean($order->payment->payment_channel->label() ?: '&mdash;')}}
                                </td>
                                <td>
                                    {!!
                                        $order->payment->status->label() ? BaseHelper::clean(
                                            $order->payment->status->toHtml()
                                        ) : '&mdash;'
                                    !!}
                                </td>
                                <td>
                                    {!! BaseHelper::clean($order->status->toHtml()) !!}
                                </td>
                                <td>
                                    {{BaseHelper::formatDate($order->created_at)}}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <table class="table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <tr>
                                                    <th class="text-center">{{ __('Image') }}</th>
                                                    <th>{{ __('Product') }}</th>
                                                    <th class="text-center">{{ __('Amount') }}</th>
                                                    <th class="text-end" style="width: 100px">{{ __('Quantity') }}</th>
                                                    <th class="price text-end">{{ __('Total') }}</th>
                                                </tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->products as $key => $orderProduct)
                                                @php
                                                    $product = get_products([
                                                        'condition' => [
                                                            'ec_products.id' => $orderProduct->product_id,
                                                        ],
                                                        'take' => 1,
                                                        'select' => ['ec_products.id', 'ec_products.images', 'ec_products.name', 'ec_products.price', 'ec_products.sale_price', 'ec_products.sale_type', 'ec_products.start_date', 'ec_products.end_date', 'ec_products.sku', 'ec_products.is_variation', 'ec_products.status', 'ec_products.order', 'ec_products.created_at'],
                                                        'include_out_of_stock_products' => true,
                                                    ]);
                                                @endphp

                                                <tr>
                                                    <td class="text-center">
                                                        <img src="{{ RvMedia::getImageUrl($orderProduct->product_image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                                            width="50" alt="{{ $orderProduct->product_name }}">
                                                    </td>
                                                    <td>
                                                        @if ($product)
                                                            {{ $product->original_product->name }} @if ($product->sku)
                                                                ({{ $product->sku }})
                                                            @endif
                                                            @if ($product->is_variation)
                                                                <p class="mb-0">
                                                                    <small>
                                                                        @php $attributes = get_product_attributes($product->id) @endphp
                                                                        @if (!empty($attributes))
                                                                            @foreach ($attributes as $attribute)
                                                                                {{ $attribute->attribute_set_title }}
                                                                                : {{ $attribute->title }}@if (!$loop->last)
                                                                                    ,
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </small>
                                                                </p>
                                                            @endif
                                                        @else
                                                            {{ $orderProduct->product_name }}
                                                        @endif

                                                        @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                                                            @foreach ($orderProduct->options as $option)
                                                                @if (!empty($option['key']) && !empty($option['value']))
                                                                    <p class="mb-0"><small>{{ $option['key'] }}:
                                                                            <strong> {{ $option['value'] }}</strong></small></p>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if (!empty($orderProduct->product_options) && is_array($orderProduct->product_options))
                                                            {!! render_product_options_info($orderProduct->product_options, $product, true) !!}
                                                        @endif
                                                    </td>
                                                    <td>{{ format_price($orderProduct->price) }}</td>
                                                    <td class="text-center">{{ $orderProduct->qty }}</td>
                                                    <td class="money text-end">
                                                        <strong>
                                                            {{ format_price($orderProduct->price * $orderProduct->qty) }}
                                                        </strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
