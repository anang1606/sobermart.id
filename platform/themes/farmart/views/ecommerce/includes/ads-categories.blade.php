@if (isset($getAds) && count($getAds) > 0)
    <div class="container-xxxl">
        <div class="css-x2z7wj">
            <div class="owl-carousel owl-theme ads-categories-carousel">
                @foreach ($getAds as $item)
                    <a href="{{ route('public.adsbanner',$item->key) }}" class="css-eys9li">
                        <img class="lazyload" src="{{ RvMedia::getImageUrl($item->image, 'large', false, RvMedia::getDefaultImage()) }}"
                            alt="">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@else
    {!! $widgets = dynamic_sidebar('products_list_sidebar') !!}

    @if (empty($widgets))
        {!! Theme::partial('page-header', ['size' => 'xxxl', 'withTitle' => false]) !!}
    @endif
@endif
