@if (count($cart) > 0)
    @foreach ($cart as $cartItem)
        @php
            $product = $cartItem->product;
        @endphp
        {!! Theme::partial('cart-mini.item', compact('cartItem', 'product')) !!}
    @endforeach
@else
    <div class="cart_no_items py-3 px-3">
        <span class="cart-empty-message">{{ __('No products in the cart.') }}</span>
    </div>
@endif
