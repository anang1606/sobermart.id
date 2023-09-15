<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;

class AhliWaris extends BaseModel
{
    protected $table = 'ahli_waris';

    public function customer(){
        return $this->hasOne(Customer::class,'id','customer_id');
    }
}
