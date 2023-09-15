@php
    $slick = [
        'rtl'            => BaseHelper::siteLanguageDirection() == 'rtl',
        'appendArrows'   => '.arrows-wrapper',
        'arrows'         => true,
        'dots'           => false,
        'autoplay'       => $shortcode->is_autoplay == 'yes',
        'infinite'        => $shortcode->infinite == 'yes',
        'autoplaySpeed'  => in_array($shortcode->autoplay_speed, theme_get_autoplay_speed_options()) ? $shortcode->autoplay_speed : 3000,
        'speed'          => 800,
        'slidesToShow'   => 8,
        'slidesToScroll' => 1,
        'responsive'     => [
            [
                'breakpoint' => 1700,
                'settings'   => [
                    'slidesToShow' => 8,
                ],
            ],
            [
                'breakpoint' => 1500,
                'settings'   => [
                    'slidesToShow' => 7,
                ],
            ],
            [
                'breakpoint' => 1442,
                'settings'   => [
                    'slidesToShow'   => 6,
                ],
            ],
            [
                'breakpoint' => 1199,
                'settings'   => [
                    'slidesToShow' => 5,
                ],
            ],
            [
                'breakpoint' => 1024,
                'settings'   => [
                    'slidesToShow' => 4,
                ],
            ],
            [
                'breakpoint' => 767,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 3,
                    'slidesToScroll' => 1,
                ],
            ],
            [
                'breakpoint' => 481,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 3,
                    'slidesToScroll' => 1,
                ],
            ],
            [
                'breakpoint' => 376,
                'settings'   => [
                    'arrows'         => false,
                    'dots'           => true,
                    'slidesToShow'   => 2,
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
    $brands = get_featured_brands();
@endphp
<div class="widget-featured-brands py-2">
    <div class="container-xxxl">
        <div class="row">
            <div class="col-12">
                <div class="row align-items-center mb-2 widget-header">
                    <h2 class="col-auto mb-0 label-category-product">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                </div>
                <div class="featured-brands__body arrows-top-right">
                    <div class="featured-brands-body slick-slides-carousel" data-slick="{{ json_encode($slick) }}">
                        @foreach ($brands as $brand)
                            <div class="featured-brand-item">
                                <div class="brand-item-body">
                                    <a class="" href="{{ $brand->url }}">
                                        <div class="brand__thumb mb-3 img-fluid-eq">
                                            {{-- <div class="img-fluid-eq__dummy"></div> --}}
                                            <div class="img-fluid-eq__wrap" style="position: relative;">
                                                <img
                                                    class="lazyload icon-brand"
                                                    src="{{ image_placeholder($brand->logo) }}"
                                                    data-src="{{ RvMedia::getImageUrl($brand->logo, null, false, RvMedia::getDefaultImage()) }}"
                                                    alt="{{ $brand->name }}"
                                                />
                                            </div>
                                        </div>
                                        <div class="brand__text">
                                            <h4 class="h6 fw-bold text-secondary text-uppercase brand__name label-brand-product">
                                                {{ $brand->name }}
                                            </h4>
                                            <div class="h5 fw-bold brand__desc">
                                                <div class="label-brand-product">
                                                    {!! BaseHelper::clean(Str::limit($brand->description, 150)) !!}
                                                </div>
                                            </div>
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
