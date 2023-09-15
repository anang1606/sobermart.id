<?php

namespace Botble\Marketplace\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportMessageController extends BaseController
{
    public function index()
    {
        $getContacts = SupportMessage::where('is_admin',0)->groupBy('from')->latest('created_at')->get();
        foreach ($getContacts as $getContact) {
            $customer = Customer::where('id',$getContact->from)->first();

            if($customer){
                $getContact->customer = $customer;
            }

            $getContact->is_active = false;
        }
        $messages = [];
        // return $getContacts;
        return view('plugins/ecommerce::support-message.index',compact('getContacts','messages'));
    }

    public function details(Request $request){
        $redux_state = json_decode(base64_decode($request->redux_state));

        $getContacts = SupportMessage::where('is_admin',0)->groupBy('from')->latest('created_at')->get();
        foreach ($getContacts as $getContact) {
            $customer = Customer::where('id',$getContact->from)->first();

            if($customer){
                $getContact->customer = $customer;

                if($getContact->customer->id === $redux_state->customer->id){
                    $getContact->is_active = true;
                }else{
                    $getContact->is_active = false;
                }
            }
        }
        $messages = SupportMessage::where([
            ['from',$redux_state->customer->id]
        ])
        ->orWhere([
            ['for',$redux_state->customer->id]
        ])
        ->orderBy('created_at','ASC')
        ->get();

        // return $messages;
        return view('plugins/ecommerce::support-message.index',compact('getContacts','messages'));
    }

    public function store(Request $request){
        $redux_state = json_decode(base64_decode($request->redux_state));

        $createMessage = new SupportMessage;
        $createMessage->for = $redux_state->customer->id;
        $createMessage->from = auth()->id();
        $createMessage->message = $request->message;
        $createMessage->is_admin = 1;
        $createMessage->save();

        // return $createMessage;
        return redirect()->back();
    }

}
