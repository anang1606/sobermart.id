<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankList extends BaseModel
{
    use SoftDeletes;

    protected $table = 'ec_bank_list';
}
