@if ($order->shipment->tracking)
    @php
        $tracking = $order->shipment->tracking;
        $sortedHistory = json_decode(json_encode($order->shipment->tracking->history), true);
        usort($sortedHistory, function ($a, $b) {
            $dateA = strtotime($a['date_time']);
            $dateB = strtotime($b['date_time']);
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
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item['date_time'])->format('d-m-Y H:i') }}
                </div>
                <div class="u4VSsO">
                    @php
                        $status = "pengiriman";
                        $detail = $tracking = $order->shipment->tracking->detail;
                    @endphp
                    <p class="_0P1byN">
                        @if ($item['status'] !== 'Paket telah diterima')
                            Pesanan dalam Pengiriman
                        @else
                            Terkirim
                            @php
                                $status = "terkirim";
                            @endphp
                        @endif
                    </p>
                    @if ($status === 'terkirim')
                    <p>
                        {{ $item['status'] }} oleh <b>{{ $detail->receiver->name }}</b>
                    </p>
                    @elseif ($item['status'] === 'Paket akan dikirim ke alamat penerima')
                    <p>
                        {{ $item['status'] }} oleh <b>{{ $item['driverName'] }}</b>
                    </p>
                    @else
                        <p>
                            {{ $item['status'] }} {{ $item['city_name'] }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif
