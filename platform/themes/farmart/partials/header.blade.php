<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
        name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">


    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
        integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

    <style>
        :root {
            --primary-font: '{{ theme_option('primary_font', 'Muli') }}', sans-serif;
            --primary-color: {{ theme_option('primary_color', '#fab528') }};
            --heading-color: {{ theme_option('heading_color', '#000') }};
            --text-color: {{ theme_option('text_color', '#000') }};
            --primary-button-color: {{ theme_option('primary_button_color', '#000') }};
            --top-header-background-color: {{ theme_option('top_header_background_color', '#f7f7f7') }};
            --middle-header-background-color: {{ theme_option('middle_header_background_color', '#fff') }};
            --bottom-header-background-color: {{ theme_option('bottom_header_background_color', '#fff') }};
            --header-text-color: {{ theme_option('header_text_color', '#000') }};
            --header-text-secondary-color: {{ BaseHelper::hexToRgba(theme_option('header_text_color', '#000'), 0.5) }};
            --header-deliver-color: {{ BaseHelper::hexToRgba(theme_option('header_deliver_color', '#000'), 0.15) }};
            --footer-text-color: {{ theme_option('footer_text_color', '#555') }};
            --footer-heading-color: {{ theme_option('footer_heading_color', '#555') }};
            --footer-hover-color: {{ theme_option('footer_hover_color', '#fab528') }};
            --footer-border-color: {{ theme_option('footer_border_color', '#dee2e6') }};
        }
    </style>

    @php
        Theme::asset()->remove('language-css');
        Theme::asset()
            ->container('footer')
            ->remove('language-public-js');
        Theme::asset()
            ->container('footer')
            ->remove('simple-slider-owl-carousel-css');
        Theme::asset()
            ->container('footer')
            ->remove('simple-slider-owl-carousel-js');
        Theme::asset()
            ->container('footer')
            ->remove('simple-slider-css');
        Theme::asset()
            ->container('footer')
            ->remove('simple-slider-js');

        $currentRouteName = Route::currentRouteName();
    @endphp

    {!! Theme::header() !!}
    {!! Theme::partial('custom-style') !!}
    {!! Theme::partial('category-style') !!}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('themes/farmart/css/buy-now.css') }}?v=1.0"> --}}
    @if ($currentRouteName === 'customer.member')
        <link rel="stylesheet" href="{{ asset('themes/farmart/css/pricing.css') }}?v=1.0">
    @endif
    <link id="ads-categories-css" rel="stylesheet" href="{{ asset('themes/farmart/css/ads-categories.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('themes/farmart/css/notification.css') }}?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css"
        integrity="sha512-UTNP5BXLIptsaj5WdKFrkFov94lDx+eBvbKyoe1YAfjeRPC+gT5kyZ10kOHCfNZqEui1sxmqvodNUx3KbuYI/A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if ($currentRouteName === 'public.index')
        <link rel="stylesheet" href="{{ asset('vendor/core/plugins/popup-ads/css/popup-ads.css') }}" />
    @endif
    @if ($currentRouteName === 'customer.voucher-wallet' || $currentRouteName === 'customer.cashback-voucher')
        <link rel="stylesheet" href="{{ asset('themes/farmart/css/voucher-styles.css') }}" />
    @endif
    @if ($currentRouteName === 'customer.gift-target')
        <link rel="stylesheet" href="{{ asset('themes/farmart/css/timeline-styles.css') }}" />
    @endif
    @if ($currentRouteName === 'customer.bantuan')
        <link rel="stylesheet" href="{{ asset('themes/farmart/css/bantuan-styles.css') }}" />
    @endif
    <style>
        .icon-category{
            width: 30px !important;
            height: 30px !important;
        }
        .icon-brand{
            width: 120px !important;
            height: 120px !important;
        }
        .price-amount bdi span{
            font-size: 15px !important;
        }
        .label-category-product{
            font-size: 22px !important;
        }
        .label-brand-product,
        .label-brand-product p{
            font-size: 16px !important;
        }
        .featured-brands__body .slick-list, .product-categories-body .slick-list{
            margin: 0 !important;
        }
        .footer-socials-container ul li a{
            background: transparent !important;
        }
        .css-ehm296-unf-btn{
            bottom: 30px !important;
        }

        @media screen and (max-width: 376px){
            .price-amount bdi span{
                font-size: 13px !important;
            }
            .top-category__thumb{
                margin-top: 0;
            }
        }
        @media screen and (max-width: 400px){

            .top-category__thumb{
                margin-top: 0 !important;
            }
			.icon-category{
                width: 20px !important;
                height: 20px !important;
            }
            .widget-product-categories .product-categories-body .product-category-item{
                height: 100px !important;
            }
			.section-slides-wrapper{
                height: 21vw;
            }

			.section-slides-wrapper.my-3 {
				height:100px;
			}

			.section-banner-wrapper.my-3 {
				height:100px;
			}
        }


		@media screen and (max-width: 700px){

            .top-category__thumb{
                margin-top: 0 !important;
            }
			.icon-category{
                width: 20px !important;
                height: 20px !important;
            }
            .widget-product-categories .product-categories-body .product-category-item{
                height: 100px !important;
            }

			.section-slides-wrapper.my-3 {
				height:130px;
			}

			.section-banner-wrapper.my-3 {
				height:130px;
			}


        }

		@media screen and (max-width: 750px){

            .top-category__thumb{
                margin-top: 0 !important;
            }
			.icon-category{
                width: 20px !important;
                height: 20px !important;
            }
            .widget-product-categories .product-categories-body .product-category-item{
                height: 100px !important;
            }

			.section-slides-wrapper.my-3 {
				height:150px;
			}

			.section-banner-wrapper.my-3 {
				height:150px;
			}


        }

		@media screen and (max-width: 800px){

            .top-category__thumb{
                margin-top: 0 !important;
            }
			.icon-category{
                width: 20px !important;
                height: 20px !important;
            }
            .widget-product-categories .product-categories-body .product-category-item{
                height: 100px !important;
            }

        }

		@media screen and (min-width: 1023px){
            .section-content.section-content__slider .section-slides-wrapper{
                height: 23vw;
            }
            .top-category__thumb{
                margin-top: -12vh;
            }
            .icon-category{
                width: 20px !important;
                height: 20px !important;
            }
            .widget-product-categories .product-categories-body .product-category-item .category-item-body .category__name{
                font-size: 10px;
            }
        }
        @media screen and (min-width: 1200px){
            .slider-custom-ads-min{
                height: 650px;
            }
            .slider-custom-ads{
                height: 350px !important;
            }
        }
        @media screen and (min-width: 1200px){
            .section-content.section-content__slider .section-slides-wrapper{
                height: 25vw;
            }
            .top-category__thumb{
                margin-top: -12vh;
            }
        }
        @media screen and (min-width: 1400px){
            .section-content.section-content__slider .section-slides-wrapper{
                height: 22vw;
            }
            .top-category__thumb{
                margin-top: -12vh;
            }
        }
        @media screen and (min-width: 1500px){
            .section-content.section-content__slider .section-slides-wrapper{
                height: 22vw;
            }
            .top-category__thumb{
                margin-top: -20vh;
            }
            .section-banner-wrapper .banner-medium .banner-item__image{
                height: 30vw;
            }
        }
        @media screen and (min-width: 1600px){
            .section-content.section-content__slider .section-slides-wrapper{
                height: 21vw;
            }
            .top-category__thumb{
                margin-top: -12vh;
            }
        }
        @media screen and (min-width: 1700px){
            .section-content.section-content__slider .section-slides-wrapper{
                height: 20vw;
            }
            .top-category__thumb{
                margin-top: -12vh;
            }
        }
        @media screen and (min-width: 1800px){
            .section-content.section-content__slider .section-slides-wrapper{
                height: 18.5vw;
            }
            .top-category__thumb{
                margin-top: -12vh;
            }
        }
        .category__thumb.img-fluid-eq .img-fluid-eq__wrap{
            position: relative;
        }

        p {
            margin: 0;
        }
        .slick-slides-carousel .product-inner{
            border: 0;
        }
        .slick-slides-carousel .product-inner:hover{
            border-width: 1px !important;
        }
    </style>
</head>

<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif
    @if (Theme::get('bodyClass')) class="{{ Theme::get('bodyClass') }}" @endif>
    @if (theme_option('preloader_enabled', 'yes') == 'yes')
        {!! Theme::partial('preloader') !!}
    @endif

    {!! Theme::partial('svg-icons') !!}
    {!! apply_filters(THEME_FRONT_BODY, null) !!}
    <header class="header header-js-handler"
        data-sticky="{{ theme_option('sticky_header_enabled', 'yes') == 'yes' ? 'true' : 'false' }}">
        <div class="header-top d-none d-lg-block">
            <div class="container-xxxl">
                <div class="row align-items-center">
                    <div class="col-6">
                        <div class="header-info">
                            {!! Menu::renderMenuLocation('header-navigation', ['view' => 'menu-default']) !!}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="header-info header-info-right">
                            <ul>
                                @if (is_plugin_active('language'))
                                    {!! Theme::partial('language-switcher') !!}
                                @endif
                                @if (is_plugin_active('ecommerce'))
                                    @if (count($currencies) > 1)
                                        <li>
                                            <a class="language-dropdown-active" href="#">
                                                <span>{{ get_application_currency()->title }}</span>
                                                <span class="svg-icon">
                                                    <svg>
                                                        <use href="#svg-icon-chevron-down"
                                                            xlink:href="#svg-icon-chevron-down"></use>
                                                    </svg>
                                                </span>
                                            </a>
                                            <ul class="language-dropdown">
                                                @foreach ($currencies as $currency)
                                                    @if ($currency->id !== get_application_currency_id())
                                                        <li>
                                                            <a
                                                                href="{{ route('public.change-currency', $currency->title) }}">
                                                                <span>{{ $currency->title }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                    @if (auth('customer')->check())
                                        <li>
                                            <a
                                                href="{{ route('customer.overview') }}">{{ auth('customer')->user()->name }}</a>
                                            <span class="d-inline-block ms-1">(<a href="{{ route('customer.logout') }}"
                                                    class="color-primary">{{ __('Logout') }}</a>)</span>
                                        </li>
                                    @else
                                        <li><a href="{{ route('customer.login') }}">{{ __('Login') }}</a></li>
                                        <li><a href="{{ route('customer.register') }}">{{ __('Register') }}</a></li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-middle">
            <div class="container-xxxl">
                <div class="header-wrapper">
                    <div class="header-items header__left">
                        @if (theme_option('logo'))
                            <div class="logo">
                                <a href="{{ route('public.index') }}">
                                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                        alt="{{ theme_option('site_title') }}" />
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="header-items header__center">
                        @if (is_plugin_active('ecommerce'))
                            <form class="form--quick-search" action="{{ route('public.products') }}"
                                data-ajax-url="{{ route('public.ajax.search-products') }}" method="get">
                                <input class="form-control input-search-product" name="q" type="text"
                                    placeholder="{{ __("I'm shopping for...") }}" autocomplete="off">
                                <button class="btn" type="submit">
                                    <span class="svg-icon">
                                        <svg>
                                            <use href="#svg-icon-search" xlink:href="#svg-icon-search"></use>
                                        </svg>
                                    </span>
                                </button>
                                <div class="panel--search-result"></div>
                            </form>
                        @endif
                    </div>
                    <div class="header-items header__right">
                        @if (is_plugin_active('ecommerce'))
                            <div class="header__extra notification" tabindex="0" role="button">
                                <div class="header__extra">
                                    <a class="btn-shopping-cart" href="{{ route('customer.notification') }}">
                                        <svg viewBox="3 2.5 14 14" x="0" y="0"
                                            class="sobermart-icons icon-notification-2">
                                            <path
                                                d="m17 15.6-.6-1.2-.6-1.2v-7.3c0-.2 0-.4-.1-.6-.3-1.2-1.4-2.2-2.7-2.2h-1c-.3-.7-1.1-1.2-2.1-1.2s-1.8.5-2.1 1.3h-.8c-1.5 0-2.8 1.2-2.8 2.7v7.2l-1.2 2.5-.2.4h14.4zm-12.2-.8.1-.2.5-1v-.1-7.6c0-.8.7-1.5 1.5-1.5h6.1c.8 0 1.5.7 1.5 1.5v7.5.1l.6 1.2h-10.3z">
                                            </path>
                                            <path d="m10 18c1 0 1.9-.6 2.3-1.4h-4.6c.4.9 1.3 1.4 2.3 1.4z"></path>
                                        </svg>
                                        <span class="header-item-counter">{{ $notification->count_unread }}</span>
                                    </a>
                                </div>
                                <div aria-describedby="stardust-popover3" role="tooltip" aria-hidden="false"
                                    class="stardust-popover__popover stardust-popover__popover--show stardust-popover__popover--border"
                                    style="top: 30px; right: 0px; transform-origin: 366.034px top;">
                                    <div class="stardust-popover__arrow"
                                        style="top: 1px; left: 366.034px; transform: translate(-7px, -100%); border-bottom: 10px solid rgba(0, 0, 0, 0.09); border-left: 0px solid transparent; border-right: 0px solid transparent;">
                                        <div class="stardust-popover__arrow--inner"
                                            style="border-bottom: 10px solid rgb(255, 255, 255); border-left: 14px solid transparent; border-right: 14px solid transparent; bottom: -10px;">
                                        </div>
                                    </div>
                                    <div class="P+Vq2G">
                                        <div class="FNlc4E">Notifikasi Baru Diterima</div>
                                        <div style="max-height: 360px;overflow: auto;">
                                            @foreach ($notification->notifications as $notif)
                                                <a href="{{ route('customer.notification.read',$notif->id) }}" class="xT-b2g eivFaM @if ($notif->is_read === 0)
                                                    ZUkA8-
                                                @endif">
                                                    <div class="_4JfVki _6Wd-Sz">
                                                        <div class="yvbeD6 _9KTDdY">
                                                            <div class="_9KTDdY vc8g9F"
                                                                style='background-image: url("{{ RvMedia::url($notif->image) }}"); background-size: contain; background-repeat: no-repeat;'>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ky1K4W qvZNVn">
                                                        <div class="JkOi+1">{{ $notif->title }}</div>
                                                        <div class="F7LO1I">
                                                            {!! $notif->description !!}
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                        <a class="PV0r10" href="{{ route('customer.notification') }}">
                                            <span>
                                                Tampilkan Semua
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if (EcommerceHelper::isWishlistEnabled())
                                <div class="header__extra header-wishlist">
                                    <a class="btn-wishlist" href="{{ route('public.wishlist') }}">
                                        <span class="svg-icon">
                                            <svg>
                                                <use href="#svg-icon-wishlist" xlink:href="#svg-icon-wishlist"></use>
                                            </svg>
                                        </span>
                                        <span class="header-item-counter">
                                            {{ auth('customer')->check()? auth('customer')->user()->wishlist()->count(): Cart::instance('wishlist')->count() }}
                                        </span>
                                    </a>
                                </div>
                            @endif
                            @if (EcommerceHelper::isCartEnabled())
                                <div class="header__extra cart--mini" tabindex="0" role="button">
                                    <div class="header__extra">
                                        <a class="btn-shopping-cart" href="{{ route('public.cart') }}">
                                            <span class="svg-icon">
                                                <svg>
                                                    <use href="#svg-icon-cart" xlink:href="#svg-icon-cart"></use>
                                                </svg>
                                            </span>
                                            <span
                                                class="header-item-counter cart-counter-item">{{ count($cart) }}</span>
                                        </a>
                                    </div>
                                    <div class="css-ix8msr">
                                        <div class="css-j7qwjs">
                                            <div class="css-1f30qus">
                                                <div class="css-b773wd">
                                                    Total (<span class="cart-counter-item">{{ count($cart) }}</span>)
                                                </div>
                                                <a href="{{ route('public.cart') }}" class="css-bjt78w">
                                                    Cart
                                                </a>
                                            </div>
                                            <div class="css-nfajfx">
                                                {!! Theme::partial('cart-mini.list') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="header-wrapper">
                <nav class="navigation">
                    <div class="container-xxxl">
                        <div class="navigation__left">
                            @if (is_plugin_active('ecommerce'))
                                <div class="menu--product-categories css-vk082c">
                                    <div class="menu__toggle">
                                        <span class="svg-icon">
                                            <svg>
                                                <use href="#svg-icon-list" xlink:href="#svg-icon-list"></use>
                                            </svg>
                                        </span>
                                        <span class="menu__toggle-title">{{ __('Kategori Belanja') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="navigation__center">
                            {!! Menu::renderMenuLocation('main-menu', [
                                'view' => 'menu',
                                'options' => ['class' => 'menu'],
                            ]) !!}
                        </div>
                        <div class="navigation__right">
                            @if (is_plugin_active('ecommerce') && EcommerceHelper::isEnabledCustomerRecentlyViewedProducts())
                                <div class="header-recently-viewed"
                                    data-url="{{ route('public.ajax.recently-viewed-products') }}" role="button">
                                    <h3 class="recently-title">
                                        <span class="svg-icon recent-icon">
                                            <svg>
                                                <use href="#svg-icon-refresh" xlink:href="#svg-icon-refresh"></use>
                                            </svg>
                                        </span>
                                        {{ __('Recently Viewed') }}
                                    </h3>
                                    <div class="recently-viewed-inner container-xxxl">
                                        <div class="recently-viewed-content">
                                            <div class="loading--wrapper">
                                                <div class="loading"></div>
                                            </div>
                                            <div class="recently-viewed-products"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="header-mobile header-js-handler"
            data-sticky="{{ theme_option('sticky_header_mobile_enabled', 'yes') == 'yes' ? 'true' : 'false' }}">
            <div class="header-items-mobile header-items-mobile--left">
                <div class="menu-mobile">
                    <div class="menu-box-title">
                        <div class="icon menu-icon toggle--sidebar" href="#menu-mobile">
                            <span class="svg-icon">
                                <svg>
                                    <use href="#svg-icon-list" xlink:href="#svg-icon-list"></use>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-items-mobile header-items-mobile--center">
                @if (theme_option('logo'))
                    <div class="logo">
                        <a href="{{ route('public.index') }}">
                            <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                alt="{{ theme_option('site_title') }}" width="155" />
                        </a>
                    </div>
                @endif
            </div>
            <div class="header-items-mobile header-items-mobile--right">
                <div class="search-form--mobile search-form--mobile-right search-panel">
                    <a class="open-search-panel toggle--sidebar" href="#search-mobile">
                        <span class="svg-icon">
                            <svg>
                                <use href="#svg-icon-search" xlink:href="#svg-icon-search"></use>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    @if (is_plugin_active('ecommerce'))
        {!! Theme::partial('product-categories-dropdown', compact('categories')) !!}
    @endif
