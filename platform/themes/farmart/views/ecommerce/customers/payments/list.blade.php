@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    @php
        $iconShop =
            'data:image/png;base64,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';
    @endphp
    <div class="css-kqc12l">
        <div class="css-xr2eo5">
            <p class="css-15yrpiz">
                {{ SeoHelper::getTitle() }}
            </p>
        </div>
        <div>
            @if (count($payments) > 0)
                @foreach ($payments as $payment)
                    <section class="css-s4jn04-unf-card eeeacht0">
                        <div>
                            <div class="css-69i1ev">
                                <div class="css-1k6yql2">
                                    <img src={{ $iconShop }} alt="product" width="24">
                                    <p class="css-1r8lly0">
                                        @if ($payment->type_status === 'paket')
                                            {{ __('Packages') }}
                                        @else
                                            {{ __('Shopping') }}
                                        @endif
                                    </p>
                                    <p class="css-8lnd9g">
                                        {{ date_format(new DateTime($payment->transaction_time), 'j M Y') }}
                                    </p>
                                </div>
                                <div class="css-1k6yql2">
                                    <p class="css-5gojna">
                                        {{ __('Paid Before') }}
                                    </p>
                                    <svg class="unf-icon" viewBox="0 0 24 24" width="14" height="14"
                                        fill="var(--Y400, #FF8B00)" style="display: inline-block; vertical-align: middle;">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M6.584 3.882A9.75 9.75 0 0112 2.24 9.76 9.76 0 0121.75 12 9.75 9.75 0 116.584 3.882zm.832 14.967A8.25 8.25 0 0012 20.24 8.26 8.26 0 0020.25 12a8.25 8.25 0 10-12.834 6.849zm5.334-7.599H16a.75.75 0 110 1.5h-4a.76.76 0 01-.75-.75V7a.75.75 0 111.5 0v4.25z">
                                        </path>
                                    </svg>
                                    <p class="css-1qmp87g">
                                        {{ date_format(new DateTime($payment->expiry_time), 'j M, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="css-1hvhwyw">
                                <div class="css-1k0s06o">
                                    <img src="{{ asset('themes/farmart/images/bank_icons/') . '/' . $payment->bank . '.png' }}"
                                        alt="payment" width="53">
                                    <div class="css-pyfdc7">
                                        <div class="css-2qg9nh">
                                            Metode Pembayaran
                                        </div>
                                        <div class="css-gcdhmf">
                                            <span style="text-transform: uppercase">{{ $payment->bank }}</span>
                                            {{ $payment->payment_channel->label() }}
                                        </div>
                                    </div>
                                    <div class="css-f3ztd">
                                        <div class="css-2qg9nh">
                                            {{ $payment->payment_channel->label() === 'Bank transfer' ? 'Bank Account Number' : 'Nomor Virtual Account' }}
                                        </div>
                                        <div class="css-gcdhmf">
                                            {{ $payment->va_number }}
                                        </div>
                                    </div>
                                </div>
                                <div class="css-j4nz03">
                                    <div class="css-2qg9nh">
                                        {{ __('Total Payment') }}
                                    </div>
                                    <div class="css-gcdhmf">
                                        {{ format_price($payment->amount) }}
                                    </div>
                                </div>
                            </div>
                            <div class="css-1bvc4cc">
                                @if ($payment->payment_channel->label() === 'Bank transfer')
                                    <button class="css-1icpry0-unf-btn upload-payment"
                                        data-uuid="{{ base64_encode($payment->id) }}"
                                        data-holder="{{ $payment->bank_holder }}">
                                        <span>Upload Bukti Pembayaran</span>
                                    </button>
                                @endif
                            </div>
                            <div class="css-1bvc4cc" style="justify-content: start;max-width:70%">
                                @if ($payment->status == 'failed')
                                    <span style="color:red">{{ $payment->notes }}</span>
                                @endif
                            </div>
                        </div>
                    </section>
                @endforeach
            @else
                <div class="css-18s3rjz">
                    <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="logo" class="css-1rahovo" />
                    <p class="css-w0kc19">{{ __('Transaction not found') }}</p>
                    <p class="css-1hnyl5u">{{ __('Come on, start shopping and fulfill various needs') }}
                        {{ theme_option('site_title') }}.</p>
                    <a href="{{ route('public.index') }}" class="btn css-1k9qobw-unf-btn">
                        {{ __('shopping') }} </a>
                </div>
            @endif
        </div>
    </div>
    <div class="modal fade" id="modalPayment" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalPaymentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalPaymentLabel">Upload Bukti Pembayaran</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input id="input-file" type="file" accept="image/*" style="display: none;"
                            data-url="{{ base64_encode(route('customer.upload-payment')) }}">
                        <div class="col-md-12">
                            <div class="alert alert-secondary" role="alert">
                                Unggah bukti pembayaran dapat mempercepat verifikasi pembayaran
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p style="margin: 0 !important">Pastikan bukti pembayaran menampilkan:</p>
                        </div>
                        <div class="col-dm-12">
                            <div class="row">
                                <div class="col-md-6 mt-2">
                                    <p style="margin: 0 !important"><b>Tanggal/Waktu Transfer</b></p>
                                    <p style="margin: 0 !important">contoh: tgl. 04/06/19 / jam 07:24:06</p>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <p style="margin: 0 !important"><b>Detail Penerima</b></p>
                                    <p style="margin: 0 !important">contoh: Transfer ke Rekening <span
                                            id="bank_holder"></span></p>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <p style="margin: 0 !important"><b>Status Berhasil</b></p>
                                    <p style="margin: 0 !important">contoh: Transfer BERHASIL, Transaksi Sukses</p>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <p style="margin: 0 !important"><b>Jumlah Transfer</b></p>
                                    <p style="margin: 0 !important">contoh: Rp 123.456,00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nomor Rekening</label>
                                        <input class="form-control" type="number" id="nomor_rekening" />
                                        <small class="text-muted">Nomor harus sesuai dengan buku tabungan</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Pengirim</label>
                                        <input class="form-control" type="text" id="nama_pengirim" />
                                        <small class="text-muted">Nama harus sesuai dengan buku tabungan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="preview-image-upload"
                            data-placeholderimg="{{ asset('themes/farmart/images/icon-uploadPlaceholder.png') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
