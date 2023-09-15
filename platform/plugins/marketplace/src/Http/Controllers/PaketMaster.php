<?php

namespace Botble\Marketplace\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Marketplace\Tables\PaketMasterTable;
use Botble\Marketplace\Forms\PaketMasterForm;
use Botble\Base\Forms\FormBuilder;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Models\PaketMaster as PaketMasterModel;
use Botble\Ecommerce\Models\PaketMasterDetails;
use Botble\Ecommerce\Models\PaketMasterGift;
use DB;
use Botble\Marketplace\Http\Requests\PaketMasterRequest;
use Botble\Marketplace\Tables\ViewMemberTable;
use Exception;
use Illuminate\Http\Request;

class PaketMaster extends BaseController
{
    public function index(PaketMasterTable $table)
    {
        page_title()->setTitle('Paket');

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle('Paket');

        return $formBuilder->create(PaketMasterForm::class)->renderForm();
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $paket = PaketMasterModel::findOrFail($id);

        page_title()->setTitle('Paket "' . $paket->name . '"');

        return $formBuilder->create(PaketMasterForm::class, ['model' => $paket])->renderForm();
    }

    public function store(PaketMasterRequest $request,BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $new_paket_master = new PaketMasterModel;
            $new_paket_master->name = $request->name;
            $new_paket_master->nominal = $request->nominal;
            $new_paket_master->description = $request->description;
            $new_paket_master->image = $request->image;
            $new_paket_master->fee_commissions = $request->fee_commissions;

            if($new_paket_master->save()){
                if(count($request->options) > 0){
                    foreach ($request->options as $option) {
                        $paket_detail = new PaketMasterGift;
                        $paket_detail->paket_id = $new_paket_master->id;
                        $paket_detail->target = $option['option_target'];
                        $paket_detail->label = $option['option_value'];
                        $paket_detail->description = $option['option_description'];

                        $paket_detail->save();
                    }
                }
            }
            DB::commit();
            return $response
            ->setPreviousUrl(route('marketplace.paket_master.index'))
            ->setNextUrl(route('marketplace.paket_master.edit', $new_paket_master->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $e) {
            return $response->setError()
            ->setData($e->getMessage());
        }
    }

    public function update(int $id, PaketMasterRequest $request, BaseHttpResponse $response)
    {
        $paket = PaketMasterModel::findOrFail($id);

        if ($paket) {
            $paket->name = $request->name;
            $paket->nominal = $request->nominal;
            $paket->description = $request->description;
            $paket->image = $request->image;
            $paket->fee_commissions = $request->fee_commissions;

            if($paket->save()){
                if(count($request->options) > 0){
                    PaketMasterGift::where('paket_id',$paket->id)->delete();
                    foreach ($request->options as $option) {
                        $paket_detail = new PaketMasterGift;
                        $paket_detail->paket_id = $paket->id;
                        $paket_detail->target = $option['option_target'];
                        $paket_detail->label = $option['option_value'];
                        $paket_detail->description = $option['option_description'];

                        $paket_detail->save();
                    }
                }
            }
        }

        return $response
            ->setPreviousUrl(route('marketplace.paket_master.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response){
        try {
            $paket = PaketMasterModel::findOrFail($id);

            $paket->delete();
            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function viewMember(int $id,ViewMemberTable $viewMemberTable){
        page_title()->setTitle('View Member');

        $viewMemberTable->id = $id;
        return $viewMemberTable->renderTable();
    }

}
