<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
class PaketMasterDetails extends BaseModel
{
    protected $table = 'ec_paket_master_details';

    protected $fillable = [
        'id_paket',
        'content',
    ];
}