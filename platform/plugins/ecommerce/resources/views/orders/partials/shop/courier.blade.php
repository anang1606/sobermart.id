<div class="css-4xk3hb">
    <div class="shop-shipping-wrapper">
        <div class="coachmark-target-wrapper-dropdown-shipping">
            <div>
                <div class="css-83gmwr">
                    <div class="ddsd-label">
                        Silahkan Pilih
                    </div>
                    <div class="ddsd false">
                        <div class="ddsd-cap">
                            <div>
                                <button class="css-lwa81l-unf-btn eg8apji0" type="button"
                                    data-unify="btnShippingDurationDropDownCap"
                                    data-testid="{{ base64_encode($store->id) }}" data-weight="{{ $total_weight }}">
                                    <span class="ddsd-span">
                                        <div class="ddsd-cap-fill">
                                            <div class="ddsd-cap-text">
                                                Pengiriman
                                            </div>
                                            <div class="ddsd-caret"></div>
                                        </div>
                                    </span>
                                </button>
                            </div>
                            <input type="hidden" class="courier_price_el" name="courier_price[]">
                            <input type="hidden" class="courier_details" name="courier_details[]">
                            <input type="hidden" class="free_shipping" name="free_shipping[]"
                                value="{{ Arr::get($sessionCheckoutData, 'shipping_amount') }}">
                            <input type="hidden" class="store_id_el" name="store_id[]" value="{{ $store->id }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
