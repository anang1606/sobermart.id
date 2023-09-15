<link rel="stylesheet" href="{{ asset('themes/farmart/css/tracking.css') }}">
<div>
    <div class="cmp831">
        <div class="DM1xQK"></div>
    </div>
    <div class="mu8SJw">
        @if (!isset($isVendor))
            <div class="_0Ihttg">
                <div class="PW9gQm">
                    alamat pengiriman
                </div>
                <div class="P9zSI">
                    <div class="g5X7k">
                        <div>{{ $order->shipping_service }}</div>
                        <div>{{ $order->shipment->shipment_id }}</div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            @if (!isset($isVendor))
                <div class="col-md-4 mb-4">
                    <div class="KZmoHt">{{ $order->address->name }}</div>
                    <div class="AnJAa1">
                        <span>{{ $order->address->phone }}</span>
                        {{ $order->address->address }},{{ $order->address->city }},{{ $order->address->state }},{{ $order->address->country }},{{ $order->address->zip_code }}
                    </div>
                </div>
            @endif
            <div class="{{ (!isset($isVendor)) ? 'col-md-8' : 'col-md-12' }}">
                <div>
                    @if (strpos($order->shipment->shipping_company_name, 'J&T') !== false)
                        @include('plugins/ecommerce::themes.includes.courier.jnt', compact('order'))
                    @else
                        @include('plugins/ecommerce::themes.includes.courier.jne', compact('order'))
                    @endif
                    @if ($order->order_histories)
                        @foreach ($order->order_histories as $histories)
                            <div class="rqUx-N">
                                <div class="_4yfsbS"></div>
                                <div class="JNurwA">
                                    <div class="rVemEI">
                                        <div class="qrqTFX"></div>
                                    </div>
                                    <div class="B3MLEe">
                                        @php
                                            $dateTimeString = $histories->created_at;
                                            $timestamp = strtotime($dateTimeString);
                                            $formattedDateTime = date('d-m-Y H:i', $timestamp);
                                        @endphp
                                        {{ $formattedDateTime }}
                                    </div>
                                    <div class="u4VSsO">
                                        <p class="_0P1byN">
                                            @switch($histories->action)
                                                @case('create_order_from_payment_page')
                                                    Pesanan Dibuat
                                                @break

                                                @case('arrange_shipment')
                                                    Sedang Dikemas
                                                @break

                                                @case('delivery')
                                                    Sedang Dikirim
                                                @break
                                            @endswitch
                                        </p>
                                        <p>{{ OrderHelper::processHistoryVariables($histories) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
