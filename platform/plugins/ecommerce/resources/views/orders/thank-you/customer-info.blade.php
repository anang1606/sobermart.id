<div class="order-customer-info">
    <h3> {{ __('Customer information') }}</h3>
    @if ($order->address->id)
        @if ($order->address->name)
            <p>
                <span class="d-inline-block">{{ __('Full name') }}:</span>
                <span class="order-customer-info-meta">{{ $order->address->name }}</span>
            </p>
        @endif

        @if ($order->address->phone)
            <p>
                <span class="d-inline-block">{{ __('Phone') }}:</span>
                <span class="order-customer-info-meta">{{ $order->address->phone }}</span>
            </p>
        @endif

        @if ($order->address->email)
            <p>
                <span class="d-inline-block">{{ __('Email') }}:</span>
                <span class="order-customer-info-meta">{{ $order->address->email }}</span>
            </p>
        @endif

        @if ($order->full_address)
            <p>
                <span class="d-inline-block">{{ __('Address') }}:</span>
                <span class="order-customer-info-meta">{{ $order->full_address }}</span>
            </p>
        @endif
    @endif

    @if (!empty($isShowShipping))
        <p>
            <span class="d-inline-block">{{ __('Shipping method') }}:</span>
            <span class="order-customer-info-meta">{{ $order->shipping_method_name }} -
                {{ format_price($order->shipping_amount) }}</span>
        </p>
    @endif
    <p>
        <span class="d-inline-block">{{ __('Payment method') }}:</span>
        <span class="order-customer-info-meta" style="text-transform: uppercase">
            {{ $order->payment->payment_channel->label() }} ({{ $order->payment->bank }})
        </span>
    </p>
    <p>
        <span class="d-inline-block">{{ __('Nomor Virtual Account') }}:</span>
        <span class="order-customer-info-meta"
            style="text-transform: uppercase">{{ $order->payment->va_number }}</span>
    </p>
    {{--  <p>
        <span class="d-inline-block">{{ __('Payment method') }}:</span>
        <span class="order-customer-info-meta" style="text-transform: uppercase">
            @switch($order->payment->payment_channel)
                @case('cstore')
                    {{ $order->payment->store }}
                @break

                @case('bank_transfer')
                    {{ $order->payment->payment_channel->label() }} ({{ $order->payment->bank }})
                @break

                @case('credit_card')
                    {{ $order->payment->payment_channel->label() }} ({{ $order->payment->bank }})
                @break
            @endswitch
        </span>
    </p>
    <p>
        @switch($order->payment->payment_channel)
            @case('cstore')
                <span class="d-inline-block">{{ __('Payment code') }}:</span>
                <span class="order-customer-info-meta"
                    style="text-transform: uppercase">{{ $order->payment->payment_code }}</span>
            @break

            @case('bank_transfer')
                <span class="d-inline-block">{{ __('Nomor Virtual Account') }}:</span>
                <span class="order-customer-info-meta"
                    style="text-transform: uppercase">{{ $order->payment->va_number }}</span>
            @break
        @endswitch
    </p>  --}}
    <p>
        <span class="d-inline-block">{{ __('Payment status') }}:</span>
        <span class="order-customer-info-meta" style="text-transform: uppercase">{!! $order->payment->status->toHtml() !!}</span>
    </p>
</div>
