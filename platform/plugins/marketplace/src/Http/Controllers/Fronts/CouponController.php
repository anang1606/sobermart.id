<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Marketplace\Enums\RevenueTypeEnum;
use EcommerceHelper;
use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Marketplace\Tables\RevenueTable;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MarketplaceHelper;

class CouponController
{    

    public function index(RevenueTable $table)
    {
        page_title()->setTitle(__('Coupons'));
        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'));
    }    
}
