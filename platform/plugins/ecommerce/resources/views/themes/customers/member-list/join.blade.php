@php
    Theme::layout('full-width');
    Theme::asset()
        ->container('footer')
        ->remove('ecommerce-utilities-js');
@endphp
<div class="container-xxxl my-5">
    <div class="row my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-5">
                    <h4>Pilihlah Rencana Harga Anda</h4>
                    <p class="text-muted">
                        Mari bergabung dengan anggota kami dan pilihlah rencana harga yang sesuai dengan kebutuhan Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <div class="input-group">
                            <input id="referral_code"
                                class="form-control @if ($errors->has('referral_code')) is-invalid @endif"
                                type="number" placeholder="Referral Code" aria-label="{{ __('Password') }}"
                                name="referral_code">
                            <button class="btn btn-outline-secondary" type="button" onclick="check_referral_join()"
                                id="button_code">
                                Check
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="load-carousel">
            <div class="owl-carousel owl-theme custom-carousel row">
                @foreach ($pakets as $paket)
                    <div class="item mr-2 ml-2" style="min-width: 250px">
                        <div class="card plan-box" style="border: none">
                            <div class="card-body">
                                <div class="card-image">
                                    <img src="{{ RvMedia::url($paket->image) }}" alt="">
                                </div>
                                <div class="card-content">
                                    <h4 class="paket-name">
                                        {{ $paket->name }}
                                    </h4>
                                    <h5 class="pricing">
                                        {{ format_price($paket->nominal) }}
                                    </h5>
                                    <p class="mt-2">
                                        {{ $paket->description }}
                                    </p>
                                </div>
                                @if (auth('customer')->check())
                                    <a href="{{ route('customer.join', ['id' => $paket->id, 'referral' => 'none']) }}"
                                        class="btn btn-info btn-join">
                                        Gabung Sekarang
                                    </a>
                                @else
                                    <a href="{{ '/register?temp_id=' . $paket->id }}" class="btn btn-info btn-join">
                                        Gabung Sekarang
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
