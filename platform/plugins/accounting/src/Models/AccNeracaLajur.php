<?php

namespace Botble\Accounting\Models;

use Illuminate\Database\Eloquent\Model;

class AccNeracaLajur extends Model
{
    protected $table = 'acc_neracalajur';
    protected $fillable = [
        'idcoa',
        'namacoa',
        'typecoa',
		'adebit',
        'akredit',
        'bdebit',
		'bkredit',
        'cdebit',
		'ckredit',
        'ddebit',
		'dkredit',
        'edebit',
		'ekredit'
    ];
}
