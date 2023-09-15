<?php

namespace Botble\Ecommerce\Widgets;

use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Base\Widgets\Card;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class RevenueCard extends Card
{
    public function getOptions(): array
    {
        $data = Payment::select([
            DB::raw('SUM(COALESCE(amount, 0)) as revenue'),
        ])
        ->whereDate('created_at', '>=', $this->startDate)
        ->whereDate('created_at', '<=', $this->endDate)
        ->selectRaw('count(id) as total, date_format(created_at, "' . $this->dateFormat . '") as period')
        ->groupBy('period')
        ->pluck('revenue')
        ->toArray();

        return [
            'series' => [
                [
                    'data' => $data,
                ],
            ],
        ];
    }

    public function getViewData():array
    {
        $startDate = clone $this->startDate;
        $endDate = clone $this->endDate;

        $currentPeriod = CarbonPeriod::create($startDate, $endDate);
        $previousPeriod = CarbonPeriod::create($startDate->subDays($currentPeriod->count()), $endDate->subDays($currentPeriod->count()));

        $revenue = Payment::whereIn('status', [PaymentStatusEnum::COMPLETED, PaymentStatusEnum::PENDING])
        ->select([
            DB::raw('SUM(COALESCE(amount, 0)) as revenue'),
            'status',
        ])
        ->whereDate('created_at', '>=', $this->startDate)
        ->whereDate('created_at', '<=', $this->endDate)
        ->groupBy('status')
        ->first();

        $currentRevenue = Payment::where('status', PaymentStatusEnum::COMPLETED)
        ->whereDate('created_at', '>=', $currentPeriod->getStartDate())
        ->whereDate('created_at', '<=', $currentPeriod->getEndDate())
        ->select([
            DB::raw('SUM(COALESCE(amount, 0)) as revenue'),
        ])
        ->pluck('revenue')
        ->toArray()[0];

        $previousRevenue = Payment::where('status', PaymentStatusEnum::COMPLETED)
        ->whereDate('created_at', '>=', $previousPeriod->getStartDate())
        ->whereDate('created_at', '<=', $previousPeriod->getEndDate())
        ->select([
            DB::raw('SUM(COALESCE(amount, 0)) as revenue'),
        ])
        ->pluck('revenue')
        ->toArray()[0];

        $result = $currentRevenue - $previousRevenue;

        $result > 0 ? $this->chartColor = '#4ade80' : $this->chartColor = '#ff5b5b';

        return array_merge(parent::getViewData(), [
            'content' => view(
                'plugins/ecommerce::reports.widgets.revenue-card',
                compact('revenue', 'result','startDate','endDate')
            )->render(),
        ]);
    }
}
