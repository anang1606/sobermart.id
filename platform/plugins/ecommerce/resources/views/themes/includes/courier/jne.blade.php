@if ($order->shipment->tracking)
    @php
        $tracking = $order->shipment->tracking;
        $sortedHistory = json_decode(json_encode($order->shipment->tracking->history), true);
        usort($sortedHistory, function ($a, $b) {
            $dateA = strtotime($a['date']);
            $dateB = strtotime($b['date']);
            return $dateB - $dateA;
        });
    @endphp

    @foreach ($sortedHistory as $key => $item)
        <div class="rqUx-N">
            <div class="_4yfsbS"></div>
            <div class="JNurwA">
                <div class="rVemEI">
                    <div class="qrqTFX"></div>
                </div>
                <div class="B3MLEe">
                    {{ $item['date'] }}
                </div>
                <div class="u4VSsO">
                    <p class="_0P1byN">
                        @if ($tracking->cnote->pod_status !== 'DELIVERED' || $key == 1)
                            Pesanan dalam Pengiriman
                        @elseif($tracking->cnote->pod_status === 'DELIVERED' && $key == 0)
                            Terkirim
                        @endif
                    </p>
                    <p>{{ str_replace(['[', ']'], ['', ''], $item['desc']) }}
                        @if ($tracking->cnote->pod_status === 'DELIVERED' && $key == 0)
                            <br>
                            <a style="color: var(--primary-color);text-decoration:underline"
                                href="{{ $tracking->cnote->photo }}">
                                Lihat Bukti
                            </a>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endforeach
@endif
