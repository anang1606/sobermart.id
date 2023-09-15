<?php

namespace Botble\Marketplace\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Traits\LocationTrait;

class ProductCategory extends BaseModel
{
    use LocationTrait;

    protected $table = 'ec_product_categories';

    protected $fillable = [
        'id',
        'name',
        'parent_id',
        'order',        
    ];   
}
