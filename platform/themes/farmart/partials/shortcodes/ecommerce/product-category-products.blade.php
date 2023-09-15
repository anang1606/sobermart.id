@if ($category)
    {{--  <?php var_dump(route('public.product-category','fruits-vegetables')) ?>  --}}
    <div class="widget-products-with-category py-2 bg-light">
        <div class="container-xxxl">
            <div class="row">
                <div class="col-12">
                    <div class="row align-items-center mb-2 widget-header">
                        <h2 class="col-auto mb-0 py-2 label-category-product">{{ $shortcode->title ?: $category->name }}</h2>
                        <div class="css-fcoe5x col-auto">
                            <a class="css-i679q5" href="{{ $category->url }}" target="_self" rel="noopener noreferrer">
                                View All
                            </a>
                        </div>
                    </div>
                    <product-category-products-component limit="{{ $shortcode->limit }}"
                        :category="{{ json_encode($category) }}"
                        {{--  :children="{{ json_encode($category->activeChildren) }}"  --}}
                         :children="[]"
                        url="{{ route('public.ajax.product-category-products') }}" all=""
                        slick_config="{{ json_encode([
                            'rtl' => BaseHelper::siteLanguageDirection() == 'rtl',
                            'appendArrows' => '.arrows-wrapper',
                            'arrows' => true,
                            'dots' => false,
                            'autoplay' => $shortcode->is_autoplay == 'yes',
                            'infinite' => $shortcode->infinite == 'yes',
                            'autoplaySpeed' => in_array($shortcode->autoplay_speed, theme_get_autoplay_speed_options())
                                ? $shortcode->autoplay_speed
                                : 3000,
                            'speed' => 800,
                            'slidesToShow' => 8,
                            'slidesToScroll' => 1,
                            'swipeToSlide' => true,
                            'responsive' => [
                                [
                                    'breakpoint' => 1400,
                                    'settings' => [
                                        'slidesToShow' => 7,
                                    ],
                                ],
                                [
                                    'breakpoint' => 1199,
                                    'settings' => [
                                        'slidesToShow' => 6,
                                    ],
                                ],
                                [
                                    'breakpoint' => 1025,
                                    'settings' => [
                                        'slidesToShow' => 5,
                                    ],
                                ],
                                [
                                    'breakpoint' => 1024,
                                    'settings' => [
                                        'slidesToShow' => 4,
                                    ],
                                ],
                                [
                                    'breakpoint' => 767,
                                    'settings' => [
                                        'arrows' => true,
                                        'dots' => false,
                                        'slidesToShow' => 2,
                                        'slidesToScroll' => 1,
                                    ],
                                ],
                            ],
                        ]) }}">
                    </product-category-products-component>
                </div>
            </div>
        </div>
    </div>
@endif
