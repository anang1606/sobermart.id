<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Models\PaketMaster;
use Botble\Ecommerce\Models\PaketMasterGift;
use Botble\Ecommerce\Models\RequestGift;
use Illuminate\Http\Request;
use Theme;
use RvMedia;

class GiftController extends PublicController
{
    public function index(){
        $pakets = MemberPaket::where('user_id', auth('customer')->id())->whereNotNull('expire_date')->with('paket.details')->get();

        foreach ($pakets as $paket) {
            $countMember = MemberPaket::where([
                ['parent',$paket->user_id],
                ['id_paket',$paket->id_paket]
            ])->count();

            if($paket->paket->details){
                foreach ($paket->paket->details as $pkt) {
                    $get_request = RequestGift::where([
                        ['customer_id',auth('customer')->id()],
                        ['paket_id',$paket->paket->id],
                        ['target',$pkt->target],
                    ])->first();

                    if($get_request){
                        $pkt->is_claim = true;
                    }else{
                        $pkt->is_claim = false;
                    }
                }
            }
            // $paket->member_count = 20;
            $paket->member_count = $countMember;
            $giftMax = PaketMasterGift::where('paket_id',$paket->paket->id)->max('target');
            $paket->gift_max = ($giftMax) ? $giftMax : 0;
        }

        // return $pakets;
        $getRequests = RequestGift::where('customer_id',auth('customer')->id())
        ->with(
            'customer',
            'paket.details'
        )
        ->get();

        foreach ($getRequests as $getRequest) {
            foreach ($getRequest->paket->details as $details) {
                if($details->target === $getRequest->target){
                    $getRequest->details = $details;
                }
            }
            $getRequest->nik_ktp = hidden_nik($getRequest->nik_ktp);
            unset($getRequest->paket->details);
        }

        // return $getRequests;
        return Theme::scope(
            'ecommerce.customers.gift.index',
            compact('pakets','getRequests'),
            'plugins/ecommerce::themes.customers.gift.index'
        )->render();
    }

    public function store(Request $request){
        $decodeData = base64_decode(base64_decode($request->state_redux));
        $decodeData = explode(',',$decodeData);

        $pakets = PaketMaster::where('id',$decodeData[0])->first();
        $pakets->gift = PaketMasterGift::where('target',$decodeData[1])->first();

        return redirect(route('customer.verified-data',['state_redux' => base64_encode(json_encode($pakets))]));
    }

    public function verifiedData(){
        return Theme::scope(
            'ecommerce.customers.gift.verified-data',
            [],
            'plugins/ecommerce::themes.customers.gift.verified-data'
        )->render();
    }

    public function storeVerifiedData(Request $request){
        $state_redux = json_decode(base64_decode($request->state_redux));

        $photo_ktp_file = $request->file('photo_ktp');
        $photo_ktp = (object)RvMedia::handleUpload($photo_ktp_file, 0, 'request_gift');

        $photo_ktp_selfi_file = $request->file('photo_ktp_selfi');
        $photo_ktp_selfi = (object)RvMedia::handleUpload($photo_ktp_selfi_file, 0, 'request_gift');

        $create_request = new RequestGift;
        $create_request->customer_id = auth('customer')->id();
        $create_request->nama = $request->name;
        $create_request->nik_ktp = $request->nik;
        $create_request->alamat = $request->alamat;
        $create_request->target = $state_redux->gift->target;
        $create_request->paket_id = $state_redux->id;
        $create_request->status = 'pending';
        $create_request->photo_ktp = $photo_ktp->data->url;
        $create_request->photo_ktp_selfi = $photo_ktp_selfi->data->url;

        if($create_request->save()){
            return redirect(route('customer.gift-target'))->with('message-member', "Request anda berhasil di kirim, Silahkan Tunggu");
        }else{
            return redirect(route('customer.gift-target'))->with('message-member', "Terjadi kesalahan, Silahkan coba lagi!!");
        }
    }
}
