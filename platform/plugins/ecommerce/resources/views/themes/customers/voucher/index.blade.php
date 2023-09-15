@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
    <div class="_2xiBC">
        <div class="RCVyH2">
            <div class="VoFc2">Voucher Saya</div>
            <div class="WO0t3c">
                <div class="qZnY9m">
                    <a href="{{ route('customer.cashback-voucher') }}" class="AtS1UR">
                        Dapatkan Voucher Lainnya
                    </a>
                </div>
                {{-- <div class="qZnY9m">
                    <a class="AtS1UR" href="/user/voucher-wallet?page=history">
                        Lihat Riwayat Voucher
                    </a>
                </div> --}}
            </div>
        </div>
        {{-- <div class="S6bcaH">
            <div class="IQwy">Tambah Voucher</div>
            <div class="input-with-validator-wrapper">
                <div class="input-with-validator">
                    <input type="text" placeholder="Masukkan kode voucher" maxlength="255" value="">
                </div>
            </div>
            <button class="Crrq8D">Simpan</button>
        </div> --}}
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
@endsection
