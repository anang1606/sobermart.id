<?php

namespace Botble\PopupAds\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PopupAds extends BaseModel
{
    protected $table = 'popup_ads';
    protected $casts = [
        'status' => BaseStatusEnum::class,
        'expired_at' => 'datetime',
    ];

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->whereDate('expired_at', '>=', Carbon::now()->toDateString());
        });
    }

    public function getExpiredAtAttribute($value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('m/d/Y');
    }
}
