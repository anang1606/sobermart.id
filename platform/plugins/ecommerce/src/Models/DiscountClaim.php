<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;

class DiscountClaim extends BaseModel
{
    protected $table = 'ec_discount_claim';

    public function voucher(){
        return $this->hasOne(Discount::class,'code','code_id');
    }

}
