<div class="summary-meta">
    @if ($product->isOutOfStock())
        <div class="product-stock out-of-stock d-inline-block">
            <label>{{ __('Availability') }}:</label>{{ __('Out of stock') }}
        </div>
    @elseif (!$product->with_storehouse_management || $product->quantity < 1)
        <div class="product-stock in-stock d-inline-block">
            <label>{{ __('Availability') }}:</label>
            <span class="label-details-product-quantity" data-warehouse="{{ __('In stock') }}"
                data-translate="{{ __(':number products available', ['number' => 0]) }}">
                {!! BaseHelper::clean($product->stock_status_html) !!}
            </span>
        </div>
    @elseif ($product->quantity)
        @if (EcommerceHelper::showNumberOfProductsInProductSingle())
            <div class="product-stock in-stock d-inline-block">
                <label>{{ __('Availability') }}:</label>
                @if ($product->quantity != 1)
                    <span class="label-details-product-quantity" data-warehouse="{{ __('In stock') }}"
                        data-translate="{{ __(':number products available', ['number' => 0]) }}">
                        {{ __(':number products available', ['number' => $product->quantity]) }}
                    </span>
                @else
                    <span class="label-details-product-quantity" data-warehouse="{{ __('In stock') }}"
                        data-translate="{{ __(':number product available', ['number' => 0]) }}">
                        {{ __(':number product available', ['number' => $product->quantity]) }}
                    </span>
                @endif
            </div>
        @else
            <div class="product-stock in-stock d-inline-block">
                <label>{{ __('Availability') }}:</label>
                <span class="label-details-product-quantity" data-warehouse="{{ __('In stock') }}"
                    data-translate="{{ __(':number products available', ['number' => 0]) }}">
                    {{ __('In stock') }}
                </span>
            </div>
        @endif
    @endif
</div>
