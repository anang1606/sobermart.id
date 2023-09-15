<?php

namespace Botble\Payment\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static PaymentMethodEnum COD()
 * @method static PaymentMethodEnum BANK_TRANSFER()
 */
class PaymentMethodEnum extends Enum
{
    public const COD = 'cod';
    public const BANK_TRANSFER = 'bank_transfer';
    public const VIRTUAL_ACCOUNT = 'virtual_account';
    public const CREDIT_CARD = 'credit_card';
    public const CSTORE = 'cstore';
    public const QRIS = 'qris';

    public static $langPath = 'plugins/payment::payment.methods';

    public function getServiceClass(): ?string
    {
        return apply_filters(PAYMENT_FILTER_GET_SERVICE_CLASS, null, $this->value);
    }
}