<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;
use Botble\Ecommerce\Models\DiscountClaim;
use Theme;
use Botble\Ecommerce\Models\Discount;
use Botble\Marketplace\Models\Store;

class VoucherController extends PublicController
{
    public function index()
    {
        $vouchersClaims = DiscountClaim::where('customer_id',auth('customer')->id())
        ->with('voucher')
        ->get();

        $vouchers = [];
        $voucherSober = [];
        $voucherStore = [];
        foreach($vouchersClaims as $vouchersClaims){
            $voucher = $vouchersClaims->voucher;
            if(!$voucher->isExpired()){
                $vouchers[] = $voucher;
                $voucher->is_claim = 1;
                $voucher->is_myVoucher = 1;

                $voucher->store = null;
                if($voucher->store_id === null){
                    $voucherSober[] = $voucher;
                }else{
                    $getStore = Store::where('id',$voucher->store_id)->with('slugable')->first();
                    if($getStore){
                        $voucher->store = $getStore;
                        $voucherStore[] = $voucher;
                    }
                }
            }
        }

        // return $vouchers;
        return Theme::scope(
            'ecommerce.customers.voucher.index',
            compact('vouchers','voucherSober','voucherStore'),
            'plugins/ecommerce::themes.customers.voucher.index'
        )->render();
    }

    public function list(){
        $vouchers = Discount::get();
        $voucherSober = [];
        $voucherStore = [];
        foreach($vouchers as $voucher){
            if(!$voucher->isExpired()){
                $getClaim = DiscountClaim::where([
                    ['code_id',$voucher->code],
                    ['customer_id',auth('customer')->id()]
                ])
                ->first();

                if($getClaim){
                    $voucher->is_claim = 1;
                }else{
                    $voucher->is_claim = 0;
                }

                $voucher->is_myVoucher = 0;
                $voucher->store = null;
                if($voucher->store_id === null){
                    $voucherSober[] = $voucher;
                }else{
                    $getStore = Store::where('id',$voucher->store_id)->first();
                    if($getStore){
                        $voucher->store = $getStore;
                        $voucherStore[] = $voucher;
                    }
                }
            }
        }
        // return $vouchers;
        return Theme::scope(
            'ecommerce.customers.voucher.index',
            compact('vouchers','voucherSober','voucherStore'),
            'plugins/ecommerce::themes.customers.voucher.list'
        )->render();
    }

    public function claim(string $code){
        $decodeId = base64_decode($code);
        $decodeId = explode('.',$decodeId);

        $getVoucher = Discount::where('code',$decodeId[0])
        ->first();

        if($getVoucher && !$getVoucher->isExpired()){
            $createClaim = new DiscountClaim;
            $createClaim->customer_id = auth('customer')->id();
            $createClaim->code_id = $decodeId[0];
            $createClaim->save();
            return redirect(route('customer.cashback-voucher'))->with('message-member', "Voucher berhasil di klaim.");
        }
        return redirect(route('customer.cashback-voucher'))->with('message-member', "Voucher gagal di klaim.");
    }
}
