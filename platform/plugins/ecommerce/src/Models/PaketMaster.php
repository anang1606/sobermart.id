<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketMaster extends BaseModel
{
    use SoftDeletes;
    protected $table = 'ec_paket_master';

    protected $fillable = [
        'name',
        'nominal',
        'fee_commissions'
    ];

    public function values(): HasMany
    {
        return $this
            ->hasMany(PaketMasterGift::class, 'paket_id');
    }

    public function details(){
        return $this->hasMany(PaketMasterGift::class,'paket_id');
    }
}
