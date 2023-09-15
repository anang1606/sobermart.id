<?php

namespace Botble\Marketplace\Http\Controllers;

use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\RequestGift;
use Botble\Marketplace\Forms\RequestGiftForm;
use Botble\Marketplace\Tables\RequestGiftTable;
use Botble\Marketplace\Tables\WithdrawalMemberTable;
use EmailHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestGiftController extends BaseController
{
    public function index(RequestGiftTable $table)
    {
        page_title()->setTitle(trans('Request Gift'));

        return $table->renderTable();
    }

    public function edit(int $id,FormBuilder $formBuilder, Request $request){
        $requestGift = RequestGift::where('id',$id)
        ->with(
            'customer',
            'paket.details'
        )
        ->first();
        foreach ($requestGift->paket->details as $details) {
            if($details->target === $requestGift->target){
                $requestGift->label = $details->label;
            }
            $requestGift->customer_name = $requestGift->customer->name;
            $requestGift->paket_name = $requestGift->paket->name . ' (' . format_price($requestGift->paket->nominal) .')';
        }
        unset($requestGift->paket->details);
        page_title()->setTitle('Request Gift "' . $requestGift->customer->name . '"');

        // return $requestGift;
        return $formBuilder->create(RequestGiftForm::class, ['model' => $requestGift])->renderForm();
    }

    public function update(int $id,Request $request,BaseHttpResponse $response){
        $requestGift = RequestGift::where('id',$id)->first();
        if($requestGift){
            $requestGift->status = $request->status;
            $requestGift->notes = $request->notes;

            $requestGift->save();
        }
        return $response
            ->setPreviousUrl(route('marketplace.request-gift.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

}
