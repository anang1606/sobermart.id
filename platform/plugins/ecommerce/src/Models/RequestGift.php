<?php

namespace Botble\Ecommerce\Models;

use Arr;
use Botble\Base\Models\BaseModel;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Lang;

class RequestGift extends BaseModel
{
    protected $table = 'request_gift';

    public function customer(){
        return $this->hasOne(Customer::class,'id','customer_id');
    }

    public function paket(){
        return $this->hasOne(PaketMaster::class,'id','paket_id');
    }

    protected $casts = [
        'status' => WithdrawalStatusEnum::class,
    ];

    public function getNextStatuses(): array
    {
        return match ($this->status->getValue()) {
            WithdrawalStatusEnum::PENDING => Arr::except(
                WithdrawalStatusEnum::labels(),
                WithdrawalStatusEnum::COMPLETED
            ),
            WithdrawalStatusEnum::PROCESSING => Arr::except(
                WithdrawalStatusEnum::labels(),
                WithdrawalStatusEnum::PENDING
            ),
            default => [$this->status->getValue() => $this->status->label()],
        };
    }

    public function getStatusHelper(): ?string
    {
        $status = $this->status->getValue();
        $key = 'plugins/marketplace::withdrawal.forms.' . $status . '_status_helper';

        return Lang::has($key) ? trans($key) : null;
    }

}
