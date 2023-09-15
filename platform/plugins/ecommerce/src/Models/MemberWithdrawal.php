<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

class MemberWithdrawal extends BaseModel
{
    protected $table = 'member_withdrawal';

    protected $fillable = [
        'customer_id',
        'amount',
        'current_balance',
        'currency',
        'description',
        'payment_channel',
        'user_id',
        'status',
        'images',
        'bank_info',
    ];

    protected $casts = [
        'status' => WithdrawalStatusEnum::class,
        'images' => 'array',
        'bank_info' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

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

    public function canEditStatus(): bool
    {
        return in_array($this->status->getValue(), [
            WithdrawalStatusEnum::PENDING,
            WithdrawalStatusEnum::PROCESSING,
        ]);
    }
}
