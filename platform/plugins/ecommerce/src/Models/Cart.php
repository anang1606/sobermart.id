<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Traits\LocationTrait;

class Cart extends BaseModel
{
    use LocationTrait;

    protected $table = 'ec_carts';

    protected $fillable = [
        'id',
        'user_id',
        'product_id',
        'attributes',
        'extras',
        'qty',
    ];

    public function product(){
        return $this->hasOne(Product::class,'id','product_id');
    }

}
