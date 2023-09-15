<div class="coOw9l">
    <div class="TMCf0u">
        @foreach ($voucherSober as $voucher)
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
            <div class="LUGhvJ">
                <div class="d6WhVp">
                    <div {{-- vc_my-wallet-page-vouchers_sellerVoucher --}} class="vc_Card_container vc_my-wallet-page-vouchers_pc {{ $className }}">
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
                                    {{-- <div class="vc_Subtitle_subTitle vc_Subtitle_defaultLine">
                                        Min. Blj Rp2,5JT
                                    </div> --}}
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
                                        @if ($voucher->is_claim === 1 && $voucher->is_myVoucher === 1)
                                            @if (!$voucher->isExpired())
                                                @if ($voucher->store)
                                                    <a href="{{ route('public.store',$voucher->store->slugable->key) }}" class="btn btn-claim">
                                                        Gunakan
                                                    </a>
                                                @else
                                                    <a href="{{ route('public.products') }}" class="btn btn-claim">
                                                        Gunakan
                                                    </a>
                                                @endif
                                            @else
                                                <button disabled="disabled" class="btn btn-claim">
                                                    Gunakan
                                                </button>
                                            @endif
                                        @elseif ($voucher->is_claim === 1 && $voucher->is_myVoucher === 0)
                                            <button disabled="disabled" class="btn btn-claim">
                                                Klaim
                                            </button>
                                        @else
                                            <a href="{{ route('customer.cashback-voucher.claim', base64_encode($voucher->code . '.' . $voucher->id)) }}"
                                                class="btn btn-claim">
                                                Klaim
                                            </a>
                                        @endif
                                    </div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
