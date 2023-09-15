@php
    $slick = [
        'rtl'            => BaseHelper::siteLanguageDirection() == 'rtl',
        'appendArrows'   => '.arrows-wrapper',
        'arrows'         => false,
        'dots'           => false,
        'autoplay'       => $shortcode->is_autoplay == 'yes',
        'infinite'       => $shortcode->infinite == 'yes',
        'autoplaySpeed'  => in_array($shortcode->autoplay_speed, theme_get_autoplay_speed_options()) ? $shortcode->autoplay_speed : 3000,
        'speed'          => 800,
        'slidesToShow'   => 11,
        'slidesToScroll' => 1,
        'responsive'     => [
            [
                'breakpoint' => 1700,
                'settings'   => [
                    'slidesToShow' => 11,
                ],
            ],
            [
                'breakpoint' => 1500,
                'settings'   => [
                    'slidesToShow' => 10,
                ],
            ],
            [
                'breakpoint' => 1442,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 9,
                    'slidesToScroll' => 1,
                ],
            ],
            [
                'breakpoint' => 1199,
                'settings'   => [
                    'slidesToShow' => 6,
                ],
            ],
            [
                'breakpoint' => 1024,
                'settings'   => [
                    'slidesToShow' => 6,
                ],
            ],
            [
                'breakpoint' => 767,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 4,
                    'slidesToScroll' => 1,
                ],
            ],
            [
                'breakpoint' => 481,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 4,
                    'slidesToScroll' => 1,
                ],
            ],
            [
                'breakpoint' => 376,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 3,
                    'slidesToScroll' => 1,
                ],
            ],
            [
                'breakpoint' => 321,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 2,
                    'slidesToScroll' => 1,
                ],
            ],
        ],
    ];

    $categories = get_featured_product_categories();
@endphp
@if ($categories->count())
    <div class="top-category__thumb @if (is_plugin_active('ads')) col-xxl-9 col-xl-8 col-md-8 @else col-md-12 @endif">
        <div class="widget-product-categories pt-5 pb-2 @if (is_plugin_active('ads')) @else col-md-12 @endif">
            <div class="container-xxxl">
                <div class="row">
                    <div class="col-md-12">
                        {{-- <div class="row align-items-center mb-2 widget-header">
                            <h2 class="col-auto mb-0 py-2">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                        </div> --}}
                        <div class="product-categories-body arrows-top-right">
                            <div data-slick="{{ json_encode($slick) }}" class="product-categories-box slick-slides-carousel">
                                @foreach ($categories as $item)
                                    <div class="product-category-item p-2">
                                        <div class="category-item-body p-2">
                                            <a class="d-block py-2" href="{{ $item->url }}">
                                                <div class="category__thumb img-fluid-eq mb-2">
                                                    {{-- <div class="img-fluid-eq__dummy"></div> --}}
                                                    <div class="img-fluid-eq__wrap img-fluid-eq__wrap-category">
                                                        <img
                                                            class="lazyload mx-auto icon-category"
                                                            data-src="{{ RvMedia::getImageUrl($item->image, 'small', false, RvMedia::getDefaultImage()) }}"
                                                            alt="{{ $item->name }}" />
                                                    </div>
                                                </div>
                                                <div class="category__text text-center py-1">
                                                    <h6 class="category__name" style="font-size: 11.8px">{{ $item->name }}</h6>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="arrows-wrapper"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
