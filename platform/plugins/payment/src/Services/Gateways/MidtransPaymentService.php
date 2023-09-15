<?php

namespace Botble\Payment\Services\Gateways;

use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Str;

class MidtransPaymentService
{
    public function execute(array $data, string $payment_channel): string
    {
        // $chargeId = Str::upper(Str::random(10));
        $chargeId = $data['payment_id'];

        $orderIds = $data['order_id'];

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'charge_id' => $chargeId,
            'order_id' => $orderIds,
            'customer_id' => $data['customer_id'],
            'customer_type' => $data['customer_type'],
            'payment_channel' => $payment_channel,
            // 'status' => $data['status'],
            'status' => PaymentStatusEnum::PENDING,
        ]);

        return $chargeId;
    }
}
