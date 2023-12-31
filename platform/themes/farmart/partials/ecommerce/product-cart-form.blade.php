<form class="cart-form" action="{{ route('public.cart.add-to-cart') }}" method="POST" style="border: none !important">
    @csrf
    @if (!empty($withVariations) && $product->variations()->count() > 0)
        <div class="pr_switch_wrap">
            {!! render_product_swatches($product, [
                'selected' => $selectedAttrs,
                'view' => Theme::getThemeNamespace() . '::views.ecommerce.attributes.swatches-renderer',
            ]) !!}
        </div>
        <div class="number-items-available" style="display: none; margin-bottom: 10px;"></div>
    @endif

    {!! render_product_options($product) !!}
    <input type="hidden" name="id" class="hidden-product-id"
        value="{{ $product->is_variation || !$product->defaultVariation->product_id ? $product->id : $product->defaultVariation->product_id }}" />
        <input type="hidden" name="hidden_flashsale_status" class="hidden-flashsale-status" value="{{ ($product->flash_sale_status) ? 1 : 0 }}" />

    @if (EcommerceHelper::isCartEnabled() || !empty($withButtons))
        {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null, $product) !!}
        <div class="product-button row">
            @if (EcommerceHelper::isCartEnabled())
                {!! Theme::partial('ecommerce.product-quantity', compact('product')) !!}
                @if (auth('customer')->check())
                    <button type="submit" name="add_to_cart" value="1"
                        style="width: 100% !important;max-width: 100% !important;flex:auto;"
                        class="btn btn-primary col-md-12 mb-2 add-to-cart-button @if ($product->isOutOfStock()) disabled @endif"
                        @if ($product->isOutOfStock()) disabled @endif title="{{ __('Add to cart') }}">
                        <span class="svg-icon">
                            <svg>
                                <use href="#svg-icon-cart" xlink:href="#svg-icon-cart"></use>
                            </svg>
                        </span>
                        <span class="add-to-cart-text ms-2">
                            {{ __('Add to cart') }}
                        </span>
                    </button>
                @else
                    @if ($product->isOutOfStock())
                        <button style="width: 100% !important;max-width: 100% !important;flex:auto;"
                            class="btn btn-primary col-md-12 mb-2 add-to-cart-button disabled" disabled
                            title="{{ __('Add to cart') }}">
                            <span class="svg-icon">
                                <svg>
                                    <use href="#svg-icon-cart" xlink:href="#svg-icon-cart"></use>
                                </svg>
                            </span>
                            <span class="add-to-cart-text ms-2">
                                {{ __('Add to cart') }}
                            </span>
                        </button>
                    @else
                        <a href="{{ route('customer.login') }}"
                            style="width: 100% !important;max-width: 100% !important;flex:auto;"
                            class="btn btn-primary col-md-12 mb-2">
                            <span class="svg-icon">
                                <svg>
                                    <use href="#svg-icon-cart" xlink:href="#svg-icon-cart"></use>
                                </svg>
                            </span>
                            <span class="add-to-cart-text ms-2">
                                {{ __('Add to cart') }}
                            </span>
                        </a>
                    @endif
                @endif

                @if (EcommerceHelper::isQuickBuyButtonEnabled() && isset($withBuyNow) && $withBuyNow)
                    @if (auth('customer')->check())
                        <button type="submit" name="checkout"
                            style="width: 100% !important;max-width: 100% !important;flex:auto;" value="1"
                            class="btn btn-primary btn-black col-md-12 mb-2 add-to-cart-button @if ($product->isOutOfStock()) disabled @endif"
                            @if ($product->isOutOfStock()) disabled @endif title="{{ __('Buy Now') }}">
                            <span class="add-to-cart-text ms-2">{{ __('Buy Now') }}</span>
                        </button>
                    @else
                        <a href="{{ route('customer.login') }}"
                            style="width: 100% !important;max-width: 100% !important;flex:auto;" value="1"
                            class="btn btn-primary btn-black col-md-12 mb-2 add-to-cart-button">
                            <span class="add-to-cart-text ms-2">{{ __('Buy Now') }}</span>
                        </a>
                    @endif
                @endif
            @endif
            @if (!empty($withButtons))
                {!! Theme::partial('ecommerce.product-loop-buttons', compact('product', 'wishlistIds')) !!}
            @endif
        </div>
    @endif
</form>
