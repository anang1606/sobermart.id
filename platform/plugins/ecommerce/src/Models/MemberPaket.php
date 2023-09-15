<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberPaket extends BaseModel
{
    use SoftDeletes;
    protected $table = 'ec_customer_pakets';

    public function paket(){
        return $this->hasOne(PaketMaster::class,'id','id_paket');
    }

    public function customer(){
        return $this->hasOne(Customer::class,'id','user_id');
    }
}
