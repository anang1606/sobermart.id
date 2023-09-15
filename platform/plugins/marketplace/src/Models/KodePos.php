<?php

namespace Botble\Marketplace\Models;

use Botble\Base\Models\BaseModel;

class KodePos extends BaseModel
{
    use LocationTrait;

    protected $table = 'kodepos';

    protected $fillable = [
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kodepos',
        
    ];   
}
