@php
    $attribute = '';
    $option = '';
@endphp
@if ($product->is_variation)
    @php
        $total_attribute = $product->variant_config->productAttributes;
    @endphp
    @foreach ($product->variant_config->productAttributes as $keyAttribute => $productAttributes)
        @php
            $attribute .= $productAttributes->title . ' ' . $productAttributes->productAttributeSet->title . ($keyAttribute !== count($total_attribute) - 1 ? ', ' : '');
        @endphp
    @endforeach
@endif
@php
    $total_option_price = 0;
@endphp
@if (count($product->cart->option) > 0)
    @foreach ($product->cart->option as $keyOption => $options)
        @if ($options->affect_type == 1)
            @php
                $total_option_price += ($product->front_sale_price_with_taxes * $options->affect_price) / 100;
            @endphp
        @else
            @php
                $total_option_price += $options->affect_price;
            @endphp
        @endif
        @php
            $option .= $options->option->name . ' ' . $options->option_value . ($keyOption !== count($product->cart->option) - 1 ? ', ' : '');
        @endphp
    @endforeach
@endif
<div class="shop-product">
    <div class="css-4xk3hb">
        <div class="shop-product__flex">
            <div class="shop-product__left">
                <div class="product-img">
                    <div class="css-m2nf2c">
                        <img src="{{ RvMedia::getImageUrl($product->original_product->image) }}"
                            alt="{{ $product->original_product->name }}" class="success fade">
                    </div>
                </div>
            </div>
            <div class="shop-product__right">
                <p class="product__name css-m3bste-unf-heading e1qvo2ff8">
                    {{ $product->original_product->name }}
                    @if ($product->is_variation)
                        - {{ $attribute }}
                    @endif
                </p>
                <div class="variant-wrapper">
                    @if ($product->is_variation)
                        <p class="variant__text css-1of93gz-unf-heading e1qvo2ff8">
                            {{ $attribute }}
                        </p>
                    @endif
                    <p class="variant__quantity css-12ydbts-unf-heading e1qvo2ff8">
                        <span>{{ $product->cart->qty }}</span> Item <span> ({{ $product->weight }} kg)</span>
                    </p>
                </div>
                @if (count($product->cart->option) > 0)
                    <div class="variant-wrapper">
                        <p class="variant__text css-1of93gz-unf-heading e1qvo2ff8">
                            {{ $option }}
                        </p>
                    </div>
                @endif
                <div class="price-wrapper">
                    @if ($product->front_sale_price != $product->price)
                        <p class="slashed-price css-rbvr5f-unf-heading e1qvo2ff8">
                            {{ format_price($product->price) }}
                        </p>
                    @endif
                    <p class="css-1fqqzz-unf-heading e1qvo2ff8">
                        {{ format_price($product->front_sale_price_with_taxes + $total_option_price * $product->cart->qty) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
