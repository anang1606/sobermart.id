@php
    $discount_amount = 0;
@endphp
@if ($store->discount_amount)
    @php
        $discount_amount = $store->discount_amount;
    @endphp
@endif
<div class="css-4xk3hb">
    <div class="css-m6di7s">
        <div class="shop-footer__subtotal">
            <div class="shop-footer__row">
                <p class="css-1fqqzz-unf-heading e1qvo2ff8">
                    Subtotal
                </p>
                <div class="sf-row-value subtotal">
                    <p class="css-1fqqzz-unf-heading e1qvo2ff8" data-unify="btnSafExpandSubtotalDetail">
                        <span class="subtotal"
                            data-subtotal="{{ $total_price - $discount_amount }}">{{ format_price($total_price - $discount_amount) }}</span>
                        <span class="subtotal__arrow"></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="css-48fkxu shop-footer__details">
            <div class="shop-footer__row subtotal">
                <div class="sf-row-label">
                    Price ({{ $total_item }} items)
                </div>
                <div class="sf-row-value">
                    {{ format_price($total_price) }}
                </div>
            </div>
            @if ($store->discount_amount)
                <div class="shop-footer__row subtotal">
                    <div class="sf-row-label">
                        Diskon
                    </div>
                    <div class="sf-row-value">
                        - {{ format_price($store->discount_amount) }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
