@php
    $biaya_app = 0;
    $coupon_discount_amount = Arr::get($sessionCheckoutData, 'coupon_discount_amount');
@endphp
<div class="css-wrkbw4 sticky">
    <div class="summary-positioner">
        <div class="css-4xk3hb">
            <div class="summary-wrapper summary-position-initiated">
                <div class="fixed-wrapper">
                    <section class="corplat-sidebar-card css-y1w77o-unf-card eeeacht0">
                        <div>
                            <div class="css-w4ndmf">
                                <div>
                                    <div class="css-vodfio modalVoucher" data-bs-toggle="modal" data-bs-target="#modalVoucher">
                                        <div class="content">
                                            <div class="css-tncl4u">
                                                <span class="css-1fs76gg">
                                                    Save more with promos
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="css-12gses5"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="css-19midj6">
                                <div class="sidebar-heading-text">
                                    Shopping summary
                                </div>
                                <div class="shopping-details-wrapper">
                                    <div class="css-izuqqr">
                                        <div class="css-12d2mry">
                                            <div class="css-rt0bne">
                                                <p class="css-1z0diop-unf-heading e1qvo2ff8">
                                                    Subtotal Produk ({{ $total_all_item }} Products)
                                                </p>
                                            </div>
                                            <div class="css-171onha">
                                                <div>
                                                    <p class="css-ue30lg-unf-heading e1qvo2ff8 count-summary"
                                                        data-testId="{{ $total_all_price }}">
                                                        {{ format_price($total_all_price) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="css-12d2mry">
                                            <div class="css-rt0bne">
                                                <p class="css-1z0diop-unf-heading e1qvo2ff8">
                                                    Kode Unik
                                                </p>
                                            </div>
                                            <div class="css-171onha">
                                                <div>
                                                    <p class="css-ue30lg-unf-heading e1qvo2ff8 count-summary"
                                                        data-testId="{{ Arr::get($sessionCheckoutData, 'code_unique') }}">
                                                        {{ format_price(Arr::get($sessionCheckoutData, 'code_unique')) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="css-12d2mry"
                                            style="display: {{ $coupon_discount_amount !== '' && $coupon_discount_amount !== 0 ? 'flex' : 'none' }}">
                                            <div class="css-rt0bne">
                                                <p class="css-1z0diop-unf-heading e1qvo2ff8">
                                                    Subtotal Diskon
                                                </p>
                                            </div>
                                            <div class="css-171onha">
                                                <div>
                                                    <p class="css-ue30lg-unf-heading e1qvo2ff8 count-summary-diskon"
                                                        data-testId="{{ $coupon_discount_amount }}">
                                                        - {{ format_price($coupon_discount_amount) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="css-12d2mry" id="shipping-fee" style="display: none;">
                                            <div class="css-rt0bne">
                                                <p class="css-1z0diop-unf-heading e1qvo2ff8">
                                                    Subtotal Pengiriman
                                                </p>
                                            </div>
                                            <div class="css-171onha">
                                                <div>
                                                    <p class="css-ue30lg-unf-heading e1qvo2ff8 shipping-fee">

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="css-12d2mry" id="shipping-fee-discount" style="display: none;">
                                            <div class="css-rt0bne">
                                                <p class="css-1z0diop-unf-heading e1qvo2ff8">
                                                    Subtotal Potongan Pengiriman
                                                </p>
                                            </div>
                                            <div class="css-171onha">
                                                <div>
                                                    <p class="css-ue30lg-unf-heading e1qvo2ff8 shipping-fee-discount">

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        {{--  <div class="css-12d2mry" style="display: none">
                                            <div class="css-rt0bne">
                                                <p class="css-1z0diop-unf-heading e1qvo2ff8">
                                                    Biaya Jasa Aplikasi
                                                </p>
                                            </div>
                                            <div class="css-171onha">
                                                <div>
                                                    <p class="css-ue30lg-unf-heading e1qvo2ff8 count-summary"
                                                        data-testId="{{ $biaya_app }}">
                                                        {{ format_price($biaya_app) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>  --}}
                                    </div>
                                </div>
                                <div class="summary-grand-total-row">
                                    <div class="sgtr__label">
                                        Shopping Total
                                    </div>
                                    <div class="sgtr__value">
                                        {{ format_price($total_all_price - $coupon_discount_amount + $biaya_app + Arr::get($sessionCheckoutData, 'code_unique')) }}
                                    </div>
                                </div>
                                <div class="css-1w4crhq">
                                    By using the insurance, I agree to the <a href="">terms and conditions</a>.
                                </div>
                                <div class="summary-main-btns-wrapper">
                                    <div class="summary-main-btn">
                                        <button class="css-1k9qobw-unf-btn eg8apji0"
                                            data-codeUnique="{{ Arr::get($sessionCheckoutData, 'code_unique') }}"
                                            data-voucherApplied="{{ Arr::get($sessionCheckoutData, 'voucher_applied') }}"
                                            data-testId="{{ base64_encode(base64_encode(session()->get('checkout_selected_cart')) . ';' . base64_encode(auth('customer')->Id())) }}"
                                            type="button" data-processing-text="Processing. Please wait..."
                                            data-uuid="{{ route('public.checkout.midtrans') }}"
                                            data-submit="choose-payment">
                                            <span>Choose Payment</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
