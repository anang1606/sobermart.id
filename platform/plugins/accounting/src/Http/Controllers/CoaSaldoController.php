<?php

namespace Botble\Accounting\Http\Controllers;

use Botble\Accounting\Forms\CoaSaldoForm;
use Botble\Accounting\Http\Requests\CoaSaldoRequest;
use Botble\Accounting\Models\CoaSaldo;
use Botble\Accounting\Tables\CoaSaldoTable;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Routing\Controller;

class CoaSaldoController extends Controller{
    public function index(CoaSaldoTable $table){
        page_title()->setTitle('Coa Saldo');

        return $table->renderTable();
    }

    public function edit(int $tahun,int $idcoa,FormBuilder $formBuilder){
        $coaSaldo = CoaSaldo::where([['idcoa',$idcoa],['tahun',$tahun]])->first();

        return $formBuilder->create(CoaSaldoForm::class,['model' => $coaSaldo])->renderForm();
    }

    public function update(CoaSaldoRequest $request,BaseHttpResponse $response){
        $coaSaldo = CoaSaldo::where([['idcoa',$request->idcoa],['tahun',$request->tahun]])->first();

        if($coaSaldo){
            $coaSaldo->kredit = $request->kredit;
            $coaSaldo->debit = $request->debit;
            $coaSaldo->save();
        }
        return $response
            ->setPreviousUrl(route('coa-saldo.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
