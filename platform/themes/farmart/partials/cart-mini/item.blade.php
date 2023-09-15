<a href="{{ route('public.cart') }}" class="css-nlvql8">
    <div class="css-1mzupek">
        <img class="lazyload" data-src="{{ RvMedia::getImageUrl($product->original_product->image) }}"
            alt="{!! BaseHelper::clean($product->original_product->name) !!}" />
    </div>
    <div class="css-6963by">
        <div class="css-14d4jsr">
            {!! BaseHelper::clean($product->original_product->name) !!}
        </div>
        <div class="css-11twmqq">
            {{ $cartItem->qty }} Barang
            @if ($product->is_variation)
                <p class="mb-0">
                    <small>
                        <small>
                            @php
                                $attribute = '';
                                $total_attribute = $product->variant_config->productAttributes;
                            @endphp

                            @foreach ($product->variant_config->productAttributes as $keyAttribute => $productAttributes)
                                @php
                                    $attribute .= $productAttributes->productAttributeSet->title . ': ' . $productAttributes->title . ($keyAttribute !== count($total_attribute) - 1 ? ', ' : '');
                                @endphp
                            @endforeach
                            ({{ $attribute }})
                        </small>
                    </small>
                </p>
            @endif
            @if (count($cartItem->option) > 0)
                <p class="mb-0">
                    <small>
                        <small>
                            @php
                                $options = '';
                                $total_option = $cartItem->option;
                            @endphp
                            @foreach ($cartItem->option as $keyOptions => $option)
                                @php
                                    $options .= $option->option->name . ': ' . $option->option_value . ($keyOptions !== count($total_option) - 1 ? ', ' : '');
                                @endphp
                            @endforeach
                            ({{ $options }})
                        </small>
                    </small>
                </p>

            @endif
        </div>
    </div>
    <div class="css-1oq5uow">
        {{ format_price($product->front_sale_price) }}
    </div>
</a>
