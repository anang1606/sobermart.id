<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Customer;
use Botble\Theme\Http\Controllers\PublicController;
use Theme;
use Botble\Ecommerce\Models\PaketMaster;
use Botble\Ecommerce\Models\MemberPaket;
use RvMedia;
use Botble\Payment\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class MemberController extends PublicController
{
    public function index()
    {
        $pakets = PaketMaster::get();
        $user = auth('customer')->user();

        $paket_id = [];
        $get_is_paket = MemberPaket::where('user_id', $user->id)->has('paket')->get();

        foreach ($get_is_paket as $member) {
            $paket_id[] = $member->id_paket;
        }

        $is_member = ($user->parent !== null) ? true : false;
        if ($is_member || count($get_is_paket) > 0) {
            $pakets = PaketMaster::whereNotIn('id', $paket_id)->get();
        }

        return Theme::scope(
            'ecommerce.customers.member-list.list',
            compact('pakets', 'is_member'),
            'plugins/ecommerce::themes.customers.member-list.join'
        )->render();
    }

    public function listAllPaket()
    {
        $pakets = PaketMaster::get();
        $user = auth('customer')->user();
        $is_member = false;

        if (auth('customer')->check()) {
            // $paket_id = [];
            // $get_is_paket = MemberPaket::where('user_id',$user->id)->has('paket')->get();

            // foreach($get_is_paket as $member){
            //     $paket_id[] = $member->id_paket;
            // }

            // $is_member = ($user->parent !== null) ? true : false;
            // if($is_member || count($get_is_paket) > 0){
            //     $pakets = PaketMaster::whereNotIn('id',$paket_id)->get();
            // }
        }

        return Theme::scope(
            'ecommerce.customers.member-list.list',
            compact('pakets', 'is_member'),
            'plugins/ecommerce::themes.customers.member-list.join'
        )->render();
    }

    public function active(int $id)
    {
        $paket_member = MemberPaket::where([
            ['id', $id],
            ['user_id', auth('customer')->user()->id]
        ])->first();

        if ($paket_member) {
            $update = [
                'is_active' => 0
            ];
            $get_all_paket = MemberPaket::where([
                ['code', $paket_member->code],
                ['user_id', auth('customer')->user()->id]
            ])->update($update);

            $paket_member->is_active = 1;
            $paket_member->save();

            return redirect(route('customer.overview'));
        }
    }

    public function getReferral(Request $request, BaseHttpResponse $response)
    {
        $data = (object)$request->input();
        $check_code = MemberPaket::where('code', $data->code)->get();

        if (count($check_code) > 0) {
            $html = '';
            foreach ($check_code as $code) {
                $paket = PaketMaster::where('id', $code->id_paket)->first();
                if ($paket) {
                    $nominal = format_price($paket->nominal);
                    $route = route('customer.join', ['id' => $paket->id, 'referral' => $code->code]);

                    $html .= '<div class="item mr-2 ml-2" style="min-width: 250px">';
                    $html .= '<div class="card plan-box" style="border: none">';
                    $html .= '<div class="card-body">';
                    $html .= '<div class="card-image">';
                    $html .= '<img src="' . RvMedia::url($paket->image) . '" alt="">';
                    $html .= '</div>';
                    $html .= '<div class="card-content">';
                    $html .= '<h4 class="paket-name">' . $paket->name . '</h4>';
                    $html .= '<h5 class="pricing">' . $nominal . '</h5>';
                    $html .= '<p class="mt-2">' . $paket->description . '</p>';
                    $html .= '</div>';
                    $html .= '<a href="' . $route . '" class="btn btn-info btn-join">';
                    $html .= 'Gabung Sekarang';
                    $html .= '</a>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            }
            return $response->setData($html);
        } else {
            $html = '';
            $memberPaket = PaketMaster::with('details')->get();
            foreach ($memberPaket as $paket) {
                $nominal = format_price($paket->nominal);
                $route = route('customer.join', ['id' => $paket->id, 'referral' => 'none']);

                $html .= '<div class="item mr-2 ml-2" style="min-width: 250px">';
                $html .= '<div class="card plan-box" style="border: none">';
                $html .= '<div class="card-body">';
                $html .= '<div class="card-image">';
                $html .= '<img src="' . RvMedia::url($paket->image) . '" alt="">';
                $html .= '</div>';
                $html .= '<div class="card-content">';
                $html .= '<h4 class="paket-name">' . $paket->name . '</h4>';
                $html .= '<h5 class="pricing">' . $nominal . '</h5>';
                $html .= '<p class="mt-2">' . $paket->description . '</p>';
                $html .= '</div>';
                $html .= '<a href="' . $route . '" class="btn btn-info btn-join">';
                $html .= 'Gabung Sekarang';
                $html .= '</a>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            return $response->setData($html);
        }
    }

    private function member_unique()
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Huruf kapital yang mungkin
        $randomLetter = $letters[rand(0, strlen($letters) - 1)]; // Pilih huruf kapital secara acak
        $randomDigits = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // Angka acak enam digit

        $data = MemberPaket::max('code');
        if ($data) {
            $urutan = (int)substr($data, -5); // Ambil 5 digit terakhir dari kode yang sudah ada
            $urutan++;
        } else {
            $urutan = 1;
        }

        $code = $randomLetter . $randomDigits . str_pad($urutan, 5, $letters, STR_PAD_LEFT);

        return $code;
    }

    public function store(int $id, $referral)
    {
        DB::beginTransaction();
        try {
            $paket_member = MemberPaket::where([
                ['user_id', auth('customer')->user()->id]
            ])->first();

            $parent = '';
            if ($referral !== 'none') {
                $parent = MemberPaket::where('code', $referral)->first();
            }

            $get_paket = PaketMaster::where('id', $id)->first();

            $checkExistPaket = MemberPaket::where([
                ['user_id', auth('customer')->user()->id],
                ['id_paket', $id]
            ])->first();

            if ($checkExistPaket) {
                return redirect(route('customer.member'))->with('message-member', "Paket $get_paket->name sudah terdaftar di akun anda!!");
            }

            $checkPayment = Payment::where([
                ['customer_id', auth('customer')->user()->id],
                ['type_status', 'paket'],
                ['amount', $get_paket->nominal],
            ])->first();

            if ($checkPayment) {
                return redirect(route('customer.payments'))->with('message-member', "Silahkan lakukan pembayaran untuk paket $get_paket->name!!");
            }


            if ($paket_member) {
                $create_member_paket = new MemberPaket;
                $create_member_paket->user_id = auth('customer')->user()->id;
                $create_member_paket->code = $paket_member->code;
                $create_member_paket->id_paket = $id;
                $create_member_paket->is_active = 0;
                $create_member_paket->uuid = $this->member_unique();
                $create_member_paket->parent = ($parent) ? $parent->user_id : null;
                if ($create_member_paket->save()) {
                    $token = bin2hex(random_bytes(75 / 2));

                    $create_payment = new Payment();
                    $create_payment->user_id = 0;
                    $create_payment->charge_id = $token;
                    $create_payment->bank = 'bca';
                    $create_payment->va_number = 1;
                    $create_payment->payment_channel = 'bank_transfer';
                    $create_payment->status = 'pending';
                    $create_payment->currency = 'IDR';
                    $create_payment->customer_type = 'Botble\Ecommerce\Models\Customer';
                    $create_payment->payment_type = 'confirm';
                    $create_payment->customer_id = auth('customer')->user()->id;
                    $create_payment->amount = $get_paket->nominal;
                    $create_payment->order_id = $create_member_paket->id;
                    $create_payment->type_status = 'paket';
                    $create_payment->expiry_time = Carbon::now()->addDay(1);

                    if ($create_payment->save()) {
                        DB::commit();
                        return redirect(route('customer.payments'));
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect(route('customer.membert'));
        }
    }

    private function member_code()
    {
        $data = MemberPaket::max('code');
        // $data = Member::max('code');
        if ($data) {
            $urutan = (int)substr($data, 11, 5);
            $urutan++;
        } else {
            $urutan = "00001";
        }
        $letter = date('Ymd');
        $code = $letter . sprintf("%05s", $urutan);
        return $code;
    }
}
