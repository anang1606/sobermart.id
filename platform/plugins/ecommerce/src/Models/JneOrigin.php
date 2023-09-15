<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;

class JneOrigin extends BaseModel
{
    protected $table = 'jne_origins';

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