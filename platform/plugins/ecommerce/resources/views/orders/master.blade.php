<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title', __('Checkout')) </title>

    @if (theme_option('favicon'))
        <link rel="shortcut icon" href="{{ RvMedia::getImageUrl(theme_option('favicon')) }}">
    @endif
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-QF7HYE1_bSDi2qMg"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    {!! Html::style('vendor/core/core/base/libraries/font-awesome/css/fontawesome.min.css') !!}
    {!! Html::style('vendor/core/plugins/ecommerce/css/front-theme.css?v=1.2.0') !!}

    @if (BaseHelper::siteLanguageDirection() == 'rtl')
        {!! Html::style('vendor/core/plugins/ecommerce/css/front-theme-rtl.css?v=1.2.0') !!}
    @endif

    {!! Html::style('vendor/core/core/base/libraries/toastr/toastr.min.css') !!}

    {!! Html::script('vendor/core/plugins/ecommerce/js/checkout.js?v=1.2.0') !!}

    @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
        <script src="{{ asset('vendor/core/plugins/location/js/location.js') }}?v=1.2.0"></script>
    @endif

    {!! apply_filters('ecommerce_checkout_header', null) !!}

    @stack('header')

    <style>
        .css-47rul3 {
            margin: 8px 0px;
            background-color: rgb(255, 255, 255);
        }

        .css-poomjs {
            display: flex;
            -webkit-box-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            align-items: center;
            padding: 16px 16px 8px;
        }

        .css-1wikf7o-unf-heading {
            display: block;
            position: relative;
            font-weight: 700;
            font-size: 1.14286rem;
            line-height: 22px;
            color: rgba(49, 53, 59, 0.96);
            text-decoration: initial;
            margin: 0px;
            font-family: "Open Sauce One", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
        }

        .css-15y7da7 {
            color: rgb(3, 172, 14);
            font-size: 12px;
            cursor: pointer;
            font-weight: 700;
        }

        .css-12af7pt {
            display: flex;
            -webkit-box-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            align-items: center;
            padding: 16px;
        }

        .pb-16 {
            padding-bottom: 16px;
        }

        .pl-16 {
            padding-left: 16px;
        }

        .pr-16 {
            padding-right: 16px;
        }

        .css-543iva:not(:last-child) {
            border-bottom: 1px solid rgb(229, 231, 233);
        }

        .css-543iva {
            margin-left: 16px;
        }

        .css-6262ts {
            padding: 12px 0px 0px;
            margin-left: 0px;
            cursor: pointer;
        }

        .css-nxj9a2 {
            display: flex;
            -webkit-box-pack: start;
            justify-content: flex-start;
            align-items: flex-start;
            flex: 1 1 0%;
            max-width: 100%;
            position: relative;
            cursor: pointer;
            background: transparent;
            -webkit-tap-highlight-color: transparent;
        }

        .css-nxj9a2.for-gateway {
            -webkit-box-align: center;
            align-items: center;
        }

        .css-1q4bfol-partial-icon {
            white-space: nowrap;
            padding: 0px 8px 12px 0px;
        }

        .css-nxj9a2.for-gateway [class*="partial-icon"] {
            padding-right: 12px;
        }

        .css-1q4bfol-partial-icon img {
            width: auto;
            max-width: 40px;
            height: auto;
            max-height: 40px;
        }

        .css-nxj9a2.for-gateway [class*="partial-icon"] img {
            width: auto;
            max-width: 40px;
        }

        .css-1hep2st-partial-content {
            width: 100%;
            display: flex;
            -webkit-box-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            align-items: center;
            flex: 1 1 0%;
            min-height: 52px;
            padding-right: 16px;
            padding-bottom: 12px;
        }

        .css-nxj9a2.with-icon [class*="partial-content"] {
            width: calc(100% - 52px);
            word-break: break-all;
        }

        .css-nxj9a2.with-icon.for-gateway [class*="partial-content"] {
            width: calc(100% - 52px);
        }

        .css-oxfdv3 {
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .css-oau6bg-partial-title {
            color: rgba(49, 53, 59, 0.96);
            font-size: 14px;
            font-weight: 700;
            line-height: 20px;
        }

        .css-1g5ei8h-partial-toggle {
            white-space: nowrap;
            padding-left: 8px;
            position: relative;
        }

        .css-obuk6n {
            position: relative;
        }

        .css-1j93tw7-unf-radio {
            display: inline-block;
        }

        .css-1j93tw7-unf-radio input[type="radio"] {
            width: 0px;
            height: 0px;
            opacity: 0;
            position: absolute;
            appearance: none;
            margin: 0px;
            padding: 0px;
        }

        .css-1j93tw7-unf-radio .radio-area {
            width: 22px;
            height: 22px;
            vertical-align: middle;
            border: 2px solid rgb(159, 166, 176);
            border-radius: 50%;
            position: relative;
            display: inline-block;
        }

        .css-1j93tw7-unf-radio input[type="radio"]:checked+.radio-area {
            border-color: rgb(3, 172, 14);
        }

        .css-1j93tw7-unf-radio .radio-area::before {
            content: "";
            position: absolute;
            inset: 2px;
            border-radius: 50%;
            transform: scale(0);
            transform-origin: center center;
            transition: transform 280ms ease 0s;
        }

        .css-1j93tw7-unf-radio input[type="radio"]:checked+.radio-area::before {
            background: rgb(3, 172, 14);
            transform: scale(0.857);
        }

        .css-1kmv04l:not(:last-child) {
            margin-bottom: 8px;
        }

        .css-1kmv04l {
            font-size: 12px;
            display: flex;
            -webkit-box-pack: justify;
            justify-content: space-between;
            align-items: flex-start;
            line-height: 20px;
            word-break: break-word;
        }

        .css-1kmv04l span:first-child {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .css-1kmv04l span {
            font-size: 14px;
            color: rgba(49, 53, 59, 0.96);
            line-height: 18px;
            white-space: nowrap;
        }

        .css-f9evma {
            height: 68px;
            width: 100%;
        }

        .css-a3z2ji {
            position: absolute;
            bottom: 0px;
            left: 0px;
            z-index: 45;
            width: 100%;
            background-color: white;
            box-shadow: rgba(49, 53, 59, 0.12) 0px -1px 6px;
        }

        .css-110q6i1-unf-card {
            display: block;
            position: relative;
            margin: 0px;
            padding: 12px 16px;
            background-color: rgb(255, 255, 255);
            overflow: hidden;
            border-radius: 0px;
            box-shadow: none;
        }

        .css-1mwn02k {
            display: flex;
            -webkit-box-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            align-items: center;
        }

        .css-656qcu {
            padding-right: 8px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            max-width: calc(100% - 120px);
        }

        .css-1348tgs {
            color: rgba(49, 53, 59, 0.96);
            font-size: 12px;
            font-weight: 700;
            line-height: 20px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .css-zng1qs {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            display: flex;
            -webkit-box-pack: start;
            justify-content: flex-start;
            -webkit-box-align: center;
            align-items: center;
            margin-right: 4px;
        }

        .css-zng1qs h4 {
            line-height: 20px;
        }

        .css-jsv7xy {
            width: 100%;
            max-width: 50%;
        }

        .payment-checkout-btn {
            color: rgb(255, 255, 255);
            font-size: 1rem;
            line-height: 18px;
            width: 100%;
            border-radius: 8px;
            font-weight: 700;
            outline: none;
            overflow: hidden;
            position: relative;
            text-overflow: ellipsis;
            transition: background 0.8s ease 0s;
            white-space: nowrap;
            display: block;
            background: radial-gradient(circle, transparent 1%, rgb(3, 172, 14) 1%) center center / 15000% rgb(3, 172, 14);
            border: none;
            text-indent: initial;
        }

        .css-1hep2st-partial-content:hover {
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('themes/farmart/css/voucher-styles.css') }}" />
    <style>
        body{
            background-color: #fff !important;
        }
        :root{
            --primary-color: #0655AF;
        }
        .vc_WarningBanner_warningBanner {
            align-items: center;
            background-color: #fff8e4;
            color: #ee4d2d;
            display: flex;
            font-size: var(--vc-warning-banner-font-size,.875rem);
            height: var(--vc-warning-banner-height,2.375rem);
            margin-top: var(--vc-warning-banner-margin-top,0);
            padding-left: var(--vc-warning-banner-padding-left-right,.625rem);
            padding-right: var(--vc-warning-banner-padding-left-right,.625rem);
            padding-top: var(--vc-warning-banner-padding-top,0);
        }

        @media screen and (max-width:513px){
            .vc_Card_card, .vc_Card_container{
                width: 100%;
            }
        }
    </style>
</head>

<body class="checkout-page" @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
    {!! apply_filters('ecommerce_checkout_body', null) !!}
    <div class="checkout-content-wrap">
        <div class="container">
            <div class="row">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('footer')

    {!! Html::script('vendor/core/plugins/ecommerce/js/utilities.js') !!}
    {!! Html::script('vendor/core/core/base/libraries/toastr/toastr.min.js') !!}

    <script type="text/javascript">
        window.messages = {
            error_header: '{{ __('Error') }}',
            success_header: '{{ __('Success') }}',
        }
    </script>

    @if (session()->has('success_msg') || session()->has('error_msg') || isset($errors))
        <script type="text/javascript">
            $(document).ready(function() {
                @if (session()->has('success_msg'))
                    MainCheckout.showNotice('success', '{{ session('success_msg') }}');
                @endif
                @if (session()->has('error_msg'))
                    MainCheckout.showNotice('error', '{{ session('error_msg') }}');
                @endif
                @if (isset($errors))
                    @foreach ($errors->all() as $error)
                        MainCheckout.showNotice('error', '{{ $error }}');
                    @endforeach
                @endif
            });
        </script>
    @endif

    {!! apply_filters('ecommerce_checkout_footer', null) !!}
    {{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>  --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>

</html>
