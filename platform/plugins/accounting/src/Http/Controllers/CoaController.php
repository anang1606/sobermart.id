<?php

namespace Botble\Accounting\Http\Controllers;

use Botble\Accounting\Tables\CoaTable;
use Illuminate\Routing\Controller;

class CoaController extends Controller{
    public function index(CoaTable $table){
        page_title()->setTitle('Coa');

        return $table->renderTable();
    }
}
