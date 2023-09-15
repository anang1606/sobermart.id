<?php

namespace Botble\Payment\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Html;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Midtrans extends BaseModel
{
    protected $table = 'midtrans';
}
