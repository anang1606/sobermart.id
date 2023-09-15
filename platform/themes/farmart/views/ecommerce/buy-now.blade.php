@php
    Theme::layout('full-width');
@endphp
{!! Theme::partial('page-header', ['size' => 'xxxl']) !!}

@if ($product)
    @php
        $attribute = '';
    @endphp
    @if ($product->is_variation)
        @php
            $total_attribute = $product->variant_config->productAttributes;
        @endphp
        @foreach ($product->variant_config->productAttributes as $keyAttribute => $productAttributes)
            @php
                $attribute .= $productAttributes->title . ' ' . $productAttributes->productAttributeSet->title . ($keyAttribute !== count($total_attribute) - 1 ? ', ' : '');
            @endphp
        @endforeach
    @endif
    <div class="css-13qxrs6 mother-container">
        <div class="mother-container-flex">
            <div class="mother-container-left-side">
                <div class="mother-container-left-side-content" style="min-height: 519px;">
                    <p class="css-110u6ic-unf-heading e1qvo2ff8" style="font-size: 1.42857rem;">
                        Beli Langsung
                    </p>
                    <section>
                        <div class="css-1thjcgk">
                            <section class="css-1gxb9at-unf-card eeeacht0">
                                <div class="css-5phtwt">
                                    <div class="content">
                                        <div class="css-cxkttv-unf-heading e1qvo2ff3"> </div>
                                        <div>
                                            <p class="message css-1fhzd98-unf-heading e1qvo2ff8">
                                                Ini halaman terakhir dari proses belanjamu. Pastikan semua sudah benar,
                                                ya.
                                                :)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </section>
                    <section>
                        <div class="css-4a8lhf">
                            <p class="css-h888qr-unf-heading e1qvo2ff8" style="font-size: 1.14286rem;">
                                Barang yang dibeli
                            </p>
                            <div>
                                <div class="css-11lwy41">
                                    <div class="css-5atgq2">
                                        <p class="css-1fqqzz-unf-heading e1qvo2ff8">
                                            {{ $product->store->name }}
                                        </p>
                                    </div>
                                    <div class="css-5atgq2">
                                        <p class="css-fkvnka-unf-heading e1qvo2ff8">
                                            {{ $product->store->city }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <div class="css-1wmljoc">
                                        <div class="css-9woeyx">
                                            <div class="css-2fi3w7">
                                                <div>
                                                    <img alt="{{ $product->name }}"
                                                        src="{{ RvMedia::getImageUrl($product->original_product->image) }}">
                                                </div>
                                                <div>
                                                    <p class="css-2c1cxd-unf-heading e1qvo2ff8">
                                                        {{ $product->name }}
                                                        @if ($product->is_variation)
                                                            - {{ $attribute }}
                                                        @endif
                                                    </p>
                                                    <div style="margin-bottom: 4px;">
                                                        <p class="css-p4rixk-unf-heading e1qvo2ff8">
                                                            <span>{{ $product->qty_buy }}</span> Item <span>
                                                                ({{ $product->total_weight }} kg)</span>
                                                        </p>
                                                        @if ($product->is_variation)
                                                            <p class="css-p4rixk-unf-heading e1qvo2ff8">
                                                                {{ $attribute }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="css-1mxf0tv">
                                                        @if ($product->front_sale_price != $product->price)
                                                            <p class="slashed-price css-rbvr5f-unf-heading e1qvo2ff8">
                                                                {{ format_price($product->price) }}
                                                            </p>
                                                        @endif
                                                        <h5 class="price-final css-1ebu8wq-unf-heading e1qvo2ff5">
                                                            {{ format_price($product->front_sale_price_with_taxes) }}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section>
                        <section>
                            <div class="css-szbvf7"></div>
                            <p class="css-1q6hnbf-unf-heading e1qvo2ff8" style="font-size: 1.14286rem;">
                                Pengiriman dan pembayaran
                            </p>
                            <div class="css-nsns2p">
                                <div class="css-10zaes1">
                                    <div class="unf-card css-1xc0csq-unf-card eh33ozn0">
                                        <div>
                                            <div class="css-7e4vat">
                                                <div class="css-1k3qxju">
                                                    <div class="box-main-content">
                                                        @include(
                                                            'plugins/ecommerce::orders.partials.address-form',
                                                            compact('sessionCheckoutData'))
                                                    </div>
                                                </div>
                                                <div class="css-1fiuwsu">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <p class="col-heading css-r2m6bv-unf-heading e1qvo2ff8">
                                                                Pilih Pengiriman
                                                            </p>
                                                            <div class="css-ihbif6" data-unify="btnShippingDurationDropDownCap">
                                                                <div class="box">
                                                                    <p class="text css-14u9bre-unf-heading e1qvo2ff8">
                                                                        Pilih Pengiriman
                                                                    </p>
                                                                    <div class="icon">
                                                                        <svg class="unf-icon" viewBox="0 0 24 24"
                                                                            width="22" height="22"
                                                                            fill="var(--color-icon-enabled, #2E3137)"
                                                                            style="display: inline-block; vertical-align: middle;">
                                                                            <path
                                                                                d="M12 15.25a.74.74 0 01-.53-.22l-5-5A.75.75 0 017.53 9L12 13.44 16.47 9a.75.75 0 011.06 1l-5 5a.74.74 0 01-.53.25z">
                                                                            </path>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                                <div class="outside-excluder"></div>
                                                            </div>
                                                            <div class="css-79elbk hidden">
                                                                <div class="css-1nt6mgp">
                                                                    <div class="css-9xsrcr">
                                                                        <p
                                                                            class="heading css-r2m6bv-unf-heading e1qvo2ff8">
                                                                            Pilih Pengiriman
                                                                        </p>
                                                                        <div>
                                                                            <div class="css-80jea2">
                                                                                <div class="flex">
                                                                                    <div class="text">
                                                                                        <p
                                                                                            class="title css-r2m6bv-unf-heading e1qvo2ff8">
                                                                                            Ninja Xpress (Rp10.000)
                                                                                        </p>
                                                                                        <p
                                                                                            class="desc css-1fhzd98-unf-heading e1qvo2ff8">
                                                                                            Estimasi tiba 10 - 12 Jun
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="css-80jea2">
                                                                                <div class="flex">
                                                                                    <div class="text">
                                                                                        <p
                                                                                            class="title css-r2m6bv-unf-heading e1qvo2ff8">
                                                                                            Ninja Xpress (Rp10.000)
                                                                                        </p>
                                                                                        <p
                                                                                            class="desc css-1fhzd98-unf-heading e1qvo2ff8">
                                                                                            Estimasi tiba 10 - 12 Jun
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="css-80jea2">
                                                                                <div class="flex">
                                                                                    <div class="text">
                                                                                        <p
                                                                                            class="title css-r2m6bv-unf-heading e1qvo2ff8">
                                                                                            Ninja Xpress (Rp10.000)
                                                                                        </p>
                                                                                        <p
                                                                                            class="desc css-1fhzd98-unf-heading e1qvo2ff8">
                                                                                            Estimasi tiba 10 - 12 Jun
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="css-80jea2">
                                                                                <div class="flex">
                                                                                    <div class="text">
                                                                                        <p
                                                                                            class="title css-r2m6bv-unf-heading e1qvo2ff8">
                                                                                            Ninja Xpress (Rp10.000)
                                                                                        </p>
                                                                                        <p
                                                                                            class="desc css-1fhzd98-unf-heading e1qvo2ff8">
                                                                                            Estimasi tiba 10 - 12 Jun
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="css-80jea2">
                                                                                <div class="flex">
                                                                                    <div class="text">
                                                                                        <p
                                                                                            class="title css-r2m6bv-unf-heading e1qvo2ff8">
                                                                                            Ninja Xpress (Rp10.000)
                                                                                        </p>
                                                                                        <p
                                                                                            class="desc css-1fhzd98-unf-heading e1qvo2ff8">
                                                                                            Estimasi tiba 10 - 12 Jun
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="css-80jea2">
                                                                                <div class="flex">
                                                                                    <div class="text">
                                                                                        <p
                                                                                            class="title css-r2m6bv-unf-heading e1qvo2ff8">
                                                                                            Ninja Xpress (Rp10.000)
                                                                                        </p>
                                                                                        <p
                                                                                            class="desc css-1fhzd98-unf-heading e1qvo2ff8">
                                                                                            Estimasi tiba 10 - 12 Jun
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="css-1fk0pbm-unf-heading e1qvo2ff8">
                                                                Estimasi tiba 9 - 12 Jun
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endif

{!! Html::script('vendor/core/plugins/ecommerce/js/checkout.js?v=1.2.0') !!}
{!! Html::script('vendor/core/plugins/ecommerce/js/utilities.js') !!}
{!! Html::script('vendor/core/core/base/libraries/toastr/toastr.min.js') !!}
<script type="text/javascript">
    window.messages = {
        error_header: '{{ __('Error') }}',
        success_header: '{{ __('Success') }}',
    }
</script>
