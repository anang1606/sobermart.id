@php
    Theme::layout('full-width');
    $products->loadMissing('defaultVariation');
@endphp

<div id="ads-container">
    @include(Theme::getThemeNamespace('views.ecommerce.includes.ads-categories'))
</div>

<div class="container-xxxl">
    <div class="row my-5">
        <div class="col-12">
            <div class="row catalog-header justify-content-between">
                <div class="col-auto catalog-header__left d-flex align-items-center">
                    <h1 class="h2 catalog-header__title d-none d-lg-block">{{ __('Shop') }}</h1>
                    <a class="d-lg-none sidebar-filter-mobile" href="#">
                        <span class="svg-icon me-2">
                            <svg>
                                <use href="#svg-icon-filter" xlink:href="#svg-icon-filter"></use>
                            </svg>
                        </span>
                        <span>{{ __('Filter') }}</span>
                    </a>
                </div>
                <div class="col-auto catalog-header__right">
                    <div class="catalog-toolbar row align-items-center">
                        @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.sort')
                        @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.layout')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-2 col-lg-3">
            <form action="{{ URL::current() }}" data-action="{{ route('public.products') }}" method="GET"
                id="products-filter-form">
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.filters')
            </form>
        </div>
        <div class="col-xxl-10 col-lg-9 products-listing position-relative">
            @include(Theme::getThemeNamespace('views.ecommerce.includes.product-items'))
        </div>
    </div>
</div>
