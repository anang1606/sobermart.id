<div class="quantity" style="width: 100% !important;max-width: 100% !important;flex:auto;padding:0">
    <label class="label-quantity">{{ __('Quantity') }}:</label>
    <div class="qty-box">
        <span class="svg-icon decrease" data-key="{{ isset($key) ? $key : '' }}">
            <svg>
                <use href="#svg-icon-decrease" xlink:href="#svg-icon-decrease"></use>
            </svg>
        </span>
        <input class="input-text qty" type="number" step="1" min="1"
            max="{{ $product->with_storehouse_management ? $product->quantity : 1000 }}" name="{{ $name ?? 'qty' }}"
            value="{{ $value ?? 1 }}" title="Qty" tabindex="0" required>
        <span class="svg-icon increase" data-key="{{ isset($key) ? $key : '' }}">
            <svg>
                <use href="#svg-icon-increase" xlink:href="#svg-icon-increase"></use>
            </svg>
        </span>
    </div>
</div>
