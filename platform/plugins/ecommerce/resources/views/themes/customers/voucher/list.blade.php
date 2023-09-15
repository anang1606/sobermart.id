@php
    Theme::layout('full-width');

    $layout = request()->input('layout') ?: theme_option('store_list_layout');

    $layout = $layout && in_array($layout, array_keys(get_store_list_layouts())) ? $layout : 'grid';
@endphp

{!! Theme::partial('page-header', ['withTitle' => false]) !!}
<div class="_2xiBC _2xiBD mb-5">
    <div class="RCVyH2">
        <div class="VoFc2"></div>
        <div class="WO0t3c">
            <div class="qZnY9m">
                <a href="{{ route('customer.voucher-wallet') }}" class="AtS1UR">
                    Voucher Saya
                </a>
            </div>
        </div>
    </div>
    <nav class="mt-4">
        <div class="stardust-tabs-header-wrapper nav nav-tabs" id="nav-tab" role="tablist" style="height: 46px; background: rgb(255, 255, 255);">
            <button class="stardust-tabs-header__tab nav-link active" id="nav-all-tab" data-bs-toggle="tab" data-bs-target="#nav-all" type="button"
                role="tab" aria-controls="nav-all" aria-selected="true">
                Semua
            </button>
            <button class="stardust-tabs-header__tab nav-link" id="nav-sober-tab" data-bs-toggle="tab" data-bs-target="#nav-sober"
                type="button" role="tab" aria-controls="nav-sober" aria-selected="false">
                {!! BaseHelper::clean(page_title()->getTitle()) !!}
            </button>
            <button class="stardust-tabs-header__tab nav-link" id="nav-toko-tab" data-bs-toggle="tab" data-bs-target="#nav-toko"
                type="button" role="tab" aria-controls="nav-toko" aria-selected="false">Toko</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab"
            tabindex="0">
            @include('plugins/ecommerce::themes.customers.voucher.includes.all',compact('vouchers'))
        </div>
        <div class="tab-pane fade" id="nav-sober" role="tabpanel" aria-labelledby="nav-sober-tab" tabindex="0">
            @include('plugins/ecommerce::themes.customers.voucher.includes.sober',compact('voucherSober'))
        </div>
        <div class="tab-pane fade" id="nav-toko" role="tabpanel" aria-labelledby="nav-toko-tab" tabindex="0">
            @include('plugins/ecommerce::themes.customers.voucher.includes.toko',compact('voucherStore'))
        </div>
    </div>
</div>
