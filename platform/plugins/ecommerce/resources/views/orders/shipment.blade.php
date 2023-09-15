@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Checkout') }}
@stop
@section('content')
    @include('plugins/ecommerce::orders.partials.custom-style')
    @php
        $logo = theme_option('logo_in_the_checkout_page') ?: theme_option('logo');
        $total_all_item = 0;
        $total_all_price = 0;
    @endphp
    @if (count($carts) > 0)
        @include('plugins/payment::partials.header')
        {!! Form::open([
            'route' => ['public.checkout.process'],
            'class' => 'checkout-form payment-checkout-form',
            'id' => 'checkout-form',
        ]) !!}
        <div class="css-ve9dke">
            <div class="css-xz6h1x">
                <div class="css-urtwg0">
                    @if ($logo)
                        <a href="{{ route('public.index') }}">
                            <img src="{{ RvMedia::getImageUrl($logo) }}" class="img-fluid" width="150" style="width: 160px !important;"
                                alt="{{ theme_option('site_title') }}" />
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="container" style="padding-top: 12vh;">
            <div class="row">
                <div class="col-md-8">
                    <div class="css-4xk3hb">
                        <h1>{{ __('Checkout') }}</h1>
                        <div class="css-2dzc0w">
                            <div class="box-heading">{{ __('Shipping information') }}</div>
                            <div class="box-main-content">
                                @include(
                                    'plugins/ecommerce::orders.partials.address-form',
                                    compact('sessionCheckoutData'))
                            </div>
                        </div>
                        <div class="css-157s6vo">
                            @foreach ($carts as $key => $cart)
                                @php
                                    $store = $cart['store'];
                                    $products = $cart['products'];
                                    $total_item = $cart['total_item'];
                                    $total_price = $cart['total_price'];
                                    $total_weight = $cart['total_weight'];

                                    $total_all_item += $cart['total_item'];
                                    $total_all_price += $cart['total_price'];
                                @endphp
                                <div>
                                    <p class="css-1jhc3ur-unf-heading e1qvo2ff8">
                                        Order {{ $key + 1 }}
                                    </p>
                                    <div class="shop-group">
                                        <div>
                                            @include(
                                                'plugins/ecommerce::orders.partials.shop.heading',
                                                compact('store'))
                                            @include(
                                                'plugins/ecommerce::orders.partials.shop.body',
                                                compact(
                                                    'products',
                                                    'total_weight',
                                                    'store',
                                                    'sessionCheckoutData'))
                                            @include(
                                                'plugins/ecommerce::orders.partials.shop.footer',
                                                compact('total_item', 'total_price', 'store'))
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    @include(
                        'plugins/ecommerce::orders.partials.shop.summary',
                        compact('total_all_item', 'total_all_price', 'sessionCheckoutData'))
                </div>
            </div>
        </div>
        <div class="css-56zl4j">
            <div class="css-dmrkw7">
                <div class="css-1dfix3h-unf-footer">
                    <div class="css-70qvj9">
                        <span class="css-1qjsill" style="width: 100p;background-size: 119px 34px;"></span>
                        <div class="css-184gfr5">
                            <span id="footer-year"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <div class="modal fade" id="modalVoucher" tabindex="-1" aria-labelledby="modalVoucherLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <form action="{{ route('public.checkout.voucher-submit') }}" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <div style="display: flex;justify-content:space-between;width: 100%;">
                                <h1 class="modal-title fs-5" id="modalVoucherLabel">Pakai Promo</h1>
                                <a href="{{ route('public.checkout.reset-voucher') }}" class="modal-title fs-5 text-muted">Reset Promo</a>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="voucher-body">
                            <p class="text-center">
                                Please wait...
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light mr-3" data-bs-dismiss="modal" aria-label="Close">
                                Nanti Saja
                            </button>
                            <button type="submit" id="submit-modalVoucher" class="btn btn-info" style="color: white;" disabled>
                                Pilih Voucher
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endif
@stop
