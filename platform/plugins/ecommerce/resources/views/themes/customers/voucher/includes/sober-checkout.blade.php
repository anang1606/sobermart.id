<div class="coOw9l">
    @if (count($voucherSober) > 0)
        @foreach ($voucherSober as $voucherS)
            <div class="iF8vqN BzObne A3W8C5">
                {{$voucherS['title']}}
            </div>
            <div class="TMCf0u">
                @foreach ($voucherS['data'] as $voucher)
                    @php
                        $className = '';
                    @endphp
                    @if ($voucher->type_option === 'shipping')
                        @php
                            $className = 'vc_my-wallet-page-vouchers_freeShippingVoucher';
                        @endphp
                    @elseif ($voucher->type_option === 'percentage')
                        @php
                            $className = 'vc_my-wallet-page-vouchers_soberVoucher';
                        @endphp
                    @endif
                    <div style="opacity:@if (!$voucher->apply) 0.5 @endif;width:100%">
                        <div class="LUGhvJ" style="width: 100%;margin-bottom:@if (!$voucher->apply) 0 @endif;">
                            <div class="d6WhVp">
                                <div class="vc_Card_container vc_my-wallet-page-vouchers_pc @if (!$voucher->apply) not-selected @endif {{ $className }} {{ $voucherS['slug'] }}" data-slug="{{ $voucherS['slug'] }}">
                                    <div class="vc_Card_card">
                                        <div class="vc_Card_left">
                                            <div class="vc_Card_sawtooth"></div>
                                        </div>
                                        <div class="vc_Card_right"></div>
                                        <div class="vc_VoucherStandardTemplate_hideOverflow"></div>
                                        <div class="vc_VoucherStandardTemplate_template">
                                            <div class="vc_VoucherStandardTemplate_left">
                                                @if ($voucher->type_option === 'shipping')
                                                    <div class="vc_Logo_imageLogo">
                                                        <img src="{{ asset('themes/farmart/images/sg-11134004-23030-3vck6lzjv6nve9.png') }}"
                                                            alt="Logo" class="vc_Logo_logo">
                                                    </div>
                                                @elseif ($voucher->type_option === 'percentage')
                                                    <div class="vc_Logo_imageLogo">
                                                        <img src="{{ RvMedia::getImageUrl('logo-sober-mart-color-grey-1.png') }}"
                                                            alt="Logo" class="vc_Logo_logo">
                                                    </div>
                                                    <div class="vc_IconText_iconText vc_IconText_defaultLine">
                                                        @if ($voucher->target === 'all-orders')
                                                            Semua Pesanan
                                                        @else
                                                            Produk Tertentu
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="vc_VoucherStandardTemplate_middle">
                                                <div class="vc_MainTitle_mainTitle">
                                                    <div class="vc_MainTitle_text vc_MainTitle_defaultLine">
                                                        {{ $voucher->title }}
                                                    </div>
                                                </div>
                                                <div class="vc_Label_label">
                                                    <div class="vc_Label_soberWalletLabel">
                                                        <div class="vc_Label_soberWalletLabelContent">
                                                            {{ $voucher->code }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($voucher->end_date !== null)
                                                    <div class="vc_Expiry_expiry vc_Expiry_validUntil">
                                                        S/D: {{ date('d.m.Y', strtotime($voucher->end_date)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="vc_VoucherStandardTemplate_right">
                                                <div></div>
                                                <div class="vc_VoucherStandardTemplate_center">
                                                    @if ($voucher->apply)
                                                        {{-- <label
                                                            for="{{ $voucher->code }}-{{ $voucher->id }}"
                                                            class="vc_RadioButton_radio {{ $voucherS['slug'] }}"
                                                            data-slug="{{ $voucherS['slug'] }}">
                                                        </label> --}}
                                                        <input type="checkbox" @if ($voucher->is_seledted)
                                                            checked
                                                        @endif class="vc_RadioButton_ {{ $voucherS['slug'] }}" name="voucher[]" value="{{ $voucher->id.','.$voucherS['slug'] }}" />
                                                    @endif
                                                </div>
                                                <div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (!$voucher->apply)
                            <div class="vc_WarningBanner_warningBanner" style="width: 97%;margin-bottom:20px;">
                                @if ($voucher->target === "specific-product")
                                    Hanya berlaku untuk produk tertentu
                                @elseif($voucher->target === "amount-minimum-order")
                                    Minimal Belanja {{ format_price($voucher->min_order_price) }}
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
    <h5 class="text-center">
        Tidak ada voucher yang kamu klaim, Silahkan kamu klaim ter
    </h5>
    @endif
</div>
