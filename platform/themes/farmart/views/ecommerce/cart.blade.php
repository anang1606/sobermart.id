<div class="row cart-page-content py-5 mt-3">
    <div class="col-12">
        @if (count($carts) > 0)
            @php
                $subTotal = 0;
                $subTotalTax = 0;
            @endphp
            <table class="table cart-form__contents" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-select" width="5%">
                            <label class="unf_checkbox css-qxx6eh-unf-checkbox">
                                <input aria-label="unf-checkbox" data-unify="Checkbox" type="checkbox" id="check-all-items"
                                    name="check-all-items" value="" class="e1chjk5t0-all">
                                <span class="unf-checkbox__area checkbox-area"></span>
                            </label>
                            <div class="ajax-count-total" data-url="{{ route('public.cart.calc-total') }}"
                                style="display: none !important"></div>
                        </th>
                        <th class="product-thumbnail"></th>
                        <th class="product-name">{{ __('Product') }}</th>
                        <th class="product-price product-md d-md-table-cell d-none">{{ __('Price') }}</th>
                        <th class="product-quantity product-md d-md-table-cell d-none">{{ __('Quantity') }}</th>
                        <th class="product-subtotal product-md d-md-table-cell d-none">{{ __('Total') }}</th>
                        <th class="product-remove"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($carts as $key => $cart)
                        @php
                            $product = $cart->product;
                            $price_option = 0;
                            $subTotalTax += (isset($product->total_taxes_percentage)) ? $product->total_taxes_percentage : 0;
                        @endphp
                        @if (count($cart->option) > 0)
                            @foreach ($cart->option as $option)
                                @php
                                    if ($option->affect_type == 1) {
                                        $price_option += ($product->front_sale_price_with_taxes * $option->affect_price) / 100;
                                    } else {
                                        $price_option += $option->affect_price;
                                    }
                                @endphp
                            @endforeach
                        @endif
                        <tr class="cart-form__cart-item cart_item">
                            <td>
                                <label class="unf_checkbox css-qxx6eh-unf-checkbox">
                                    <input data-tax="{{ $subTotalTax }}"
                                        @if (in_array($cart->id, $cart_selecteds)) checked @endif
                                        aria-label="unf-checkbox" data-unify="Checkbox" type="checkbox"
                                        id="check-all-items" name="check-all-items[]" value="{{ $cart->id }}"
                                        class="e1chjk5t0">
                                    <span class="unf-checkbox__area checkbox-area"></span>
                                </label>
                            </td>
                            <td class="product-thumbnail">
                                <a href="{{ $product->original_product->url }}"
                                    style="max-width: 74px; display: inline-block;">
                                    <img src="{{ RvMedia::getImageUrl($product->original_product->image) }}" alt="{{ BaseHelper::clean($product->original_product->name) }}">
                                </a>
                            </td>
                            <td class="product-name d-md-table-cell d-block" data-title="{{ __('Product') }}">
                                <a
                                    href="{{ $product->original_product->url }}">{!! BaseHelper::clean($product->original_product->name) !!}</a>
                                @if (is_plugin_active('marketplace') && $product->original_product->store->id)
                                    <div class="variation-group">
                                        <span class="text-secondary">{{ __('Vendor') }}: </span>
                                        <span class="text-primary ms-1">
                                            <a
                                                href="{{ $product->original_product->store->url }}">{{ $product->original_product->store->name }}</a>
                                        </span>
                                    </div>
                                @endif
                                <p class="mb-0">
                                    <small>
                                        @if ($product->is_variation)
                                            @php
                                                $attribute = '';
                                                $total_attribute = $product->variant_config->productAttributes;
                                            @endphp

                                            @foreach ($product->variant_config->productAttributes as $keyAttribute => $productAttributes)
                                                @php
                                                    $attribute .= $productAttributes->productAttributeSet->title . ' ' . $productAttributes->title . ($keyAttribute !== count($total_attribute) - 1 ? ', ' : '');
                                                @endphp
                                            @endforeach
                                            ({{ $attribute }})
                                        @endif
                                    </small>
                                </p>
                                @if (count($cart->option) > 0)
                                    <small style="display: block;">
                                        {{ trans('plugins/ecommerce::product-option.price') }}
                                        <strong style="float: right;">
                                            {{ format_price($product->front_sale_price_with_taxes) }}
                                        </strong>
                                    </small>
                                    @foreach ($cart->option as $option)
                                        @php
                                            $price_option_ = 0;
                                            if ($option->affect_type == 1) {
                                                $price_option_ += ($product->front_sale_price_with_taxes * $option->affect_price) / 100;
                                            } else {
                                                $price_option_ += $option->affect_price;
                                            }
                                        @endphp
                                        <small style="display: block;">
                                            {{ $option->option->name }} :
                                            <strong>{{ $option->option_value }}</strong>
                                            <strong style="float: right;">
                                                +
                                                {{ format_price($price_option_) }}
                                            </strong>
                                        </small>
                                    @endforeach
                                @endif
                            </td>
                            <td class="product-price product-md d-md-table-cell d-block" data-title="Price">
                                <div class="box-price">
                                    <span class="d-md-none title-price">{{ __('Price') }}: </span>
                                    <span class="quantity">
                                        <span class="price-amount amount">
                                            <bdi>{{ format_price($product->front_sale_price_with_taxes + $price_option) }}
                                                @if ($product->front_sale_price != $product->price)
                                                    <small><del>{{ format_price($product->price) }}</del></small>
                                                @endif
                                            </bdi>
                                        </span>
                                    </span>
                                </div>
                            </td>
                            <td class="product-quantity product-md d-md-table-cell d-block"
                                data-title="{{ __('Quantity') }}">
                                <form class="form--shopping-cart cart-form" method="post"
                                    action="{{ route('public.cart.update') }}" id="form-cart-{{ $key }}">
                                    @csrf
                                    <input type="hidden" name="rowId" value="{{ $cart->id }}">
                                    <input type="hidden" name="productId" value="{{ $product->id }}">
                                    <div class="product-button">
                                        {!! Theme::partial(
                                            'ecommerce.product-quantity',
                                            compact('product') + [
                                                'name' => 'qty',
                                                'key' => 'form-cart-' . $key,
                                                'value' => $cart->qty,
                                            ],
                                        ) !!}
                                    </div>
                                </form>
                            </td>
                            <td class="product-subtotal product-md d-md-table-cell d-block"
                                data-title="{{ __('Total') }}">
                                <div class="box-price">
                                    <span class="d-md-none title-price">{{ __('Total') }}: </span>
                                    <span class="fw-bold amount">
                                        @php
                                            $subTotal += ($product->front_sale_price + $price_option) * $cart->qty;
                                        @endphp
                                        <span
                                            class="price-current">{{ format_price(($product->front_sale_price_with_taxes + $price_option) * $cart->qty) }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="product-remove">
                                <a class="fs-4 remove btn remove-cart-item" href="#"
                                    data-url="{{ route('public.cart.remove', $cart->id) }}"
                                    aria-label="Remove this item">
                                    <span class="svg-icon">
                                        <svg>
                                            <use href="#svg-icon-trash" xlink:href="#svg-icon-trash"></use>
                                        </svg>
                                    </span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="css-18s3rjz">
                <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="logo" class="css-1rahovo" />
                <p class="css-w0kc19">{{ __('Your cart is empty!') }}</p>
                <p class="css-1hnyl5u">{{ __('Come on, start shopping and fulfill various needs') }}
                    {{ theme_option('site_title') }}.</p>
                <a href="{{ route('public.index') }}" class="btn css-1k9qobw-unf-btn">
                    {{ __('shopping') }} </a>
            </div>
        @endif
        @if (count($carts) > 0)
            <form action="{{ route('public.cart.proses-checkout') }}" method="POST" id="main-form">
                @csrf
                <input type="hidden" name="selected_cart">
                <div class="actions my-4 pb-4 border-bottom">
                    <div class="actions__button-wrapper row justify-content-between">
                        <div class="col-md-9">
                            <div class="actions__left d-grid d-md-block">
                                <a class="btn btn-secondary mb-2" href="{{ route('public.products') }}">
                                    <span class="svg-icon">
                                        <svg>
                                            <use href="#svg-icon-arrow-left" xlink:href="#svg-icon-arrow-left"></use>
                                        </svg>
                                    </span> {{ __('Continue Shopping') }}
                                </a>
                                <a class="btn btn-secondary mb-2 ms-md-2" href="{{ route('public.index') }}">
                                    <span class="svg-icon">
                                        <svg>
                                            <use href="#svg-icon-home" xlink:href="#svg-icon-home"></use>
                                        </svg>
                                    </span> {{ __('Back to Home') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-4 col-md-5 col-coupon form-coupon-wrapper">
                        {{-- <div class="coupon">
                            <label for="coupon_code">
                                <h4>{{ __('Using A Promo Code?') }}</h4>
                            </label>
                            <div class="coupon-input input-group my-3">
                                <input class="form-control coupon-code" type="text" name="coupon_code"
                                    value="{{ old('coupon_code') }}" placeholder="{{ __('Enter coupon code') }}">
                                <button class="btn btn-primary lh-1 btn-apply-coupon-code" type="button"
                                    data-url="{{ route('public.coupon.apply') }}">{{ __('Apply') }}</button>
                            </div>
                        </div> --}}
                    </div>
                    <div class="col-lg-4 col-md-2"></div>
                    <div class="col-lg-4 col-md-5">
                        <div class="cart_totals bg-light p-4 rounded">
                            <h5 class="mb-3">{{ __('Cart totals') }}</h5>
                            <div class="cart_totals-table">
                                <div class="cart-subtotal d-flex justify-content-between border-bottom pb-3 mb-3">
                                    <span class="title fw-bold">{{ __('Subtotal') }}:</span>
                                    <span class="amount fw-bold">
                                        <span class="price-current price-current__sub-total"
                                            data-format="{{ format_price(0) }}">
                                            {{ format_price(0) }}
                                        </span>
                                    </span>
                                </div>
                                @if (EcommerceHelper::isTaxEnabled())
                                    <div class="cart-subtotal d-flex justify-content-between border-bottom pb-3 mb-3">
                                        <span class="title fw-bold">{{ __('Tax') }}:</span>
                                        <span class="amount fw-bold">
                                            <span class="price-current price-current__sub-tax"
                                                data-format="{{ format_price(0) }}">{{ format_price(0) }}</span>
                                        </span>
                                    </div>
                                @endif
                                @if ($couponDiscountAmount > 0 && session('applied_coupon_code'))
                                    <div class="cart-subtotal d-flex justify-content-between border-bottom pb-3 mb-3">
                                        <span class="title">
                                            <span
                                                class="fw-bold">{{ __('Coupon code: :code', ['code' => session('applied_coupon_code')]) }}</span>
                                            (<small>
                                                <a class="btn-remove-coupon-code text-danger"
                                                    data-url="{{ route('public.coupon.remove') }}" href="#"
                                                    data-processing-text="{{ __('Removing...') }}">{{ __('Remove') }}</a>
                                            </small>)
                                        </span>

                                        <span class="amount fw-bold price-current__sub-coupon"
                                            data-format="{{ format_price(0) }}">{{ format_price($couponDiscountAmount) }}</span>
                                    </div>
                                @endif
                                @if ($promotionDiscountAmount)
                                    <div class="ps-block__header">
                                        <p>{{ __('Discount promotion') }} <span>
                                                {{ format_price($promotionDiscountAmount) }}</span></p>
                                    </div>
                                @endif
                                <div class="order-total d-flex justify-content-between pb-3 mb-3">
                                    <span class="title">
                                        <h6 class="mb-0">{{ __('Total') }}</h6>
                                        <small>{{ __('(Shipping fees not included)') }}</small>
                                    </span>
                                    <span class="amount fw-bold fs-6 text-green">
                                        <span class="price-current price-current__total"
                                            data-format="{{ format_price(0) }}">
                                            {{ format_price(0) }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            @if (session('tracked_start_checkout'))
                                <div class="proceed-to-checkout">
                                    <div class="d-grid gap-2">
                                        <button class="btn-primary" type="submit">
                                            {{ __('Proceed to checkout') }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
