<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Traits\LocationTrait;

class JneSupport extends BaseModel
{
    // use LocationTrait;

    protected $table = 'jne_destinations';

    protected $fillable = [
        'country_name',
        'province_name',
        'city_name',
        'district_name',
        'subdistrict_name',
        'zip_code',
        'tarif_code',
    ];
}