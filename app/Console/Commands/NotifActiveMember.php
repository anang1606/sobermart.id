<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\BroadcastCustomer;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class NotifActiveMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif-active-member';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'handle notification expired member';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $getMemberPaket = MemberPaket::whereNotNull('expire_date')->with('paket')->get();
            foreach ($getMemberPaket as $mp) {
                $expireDate = Carbon::parse($mp->expire_date);
                $currentTime = Carbon::now();

                $daysDifference = $expireDate->diffInDays(Carbon::now());
                if ($daysDifference <= 14 && $expireDate->isBefore(Carbon::now())) {
                    $isMember = Customer::where([['id', $mp->user_id]])->first();
                    $checkBroadcast = Broadcast::where('title', 'Pengingat Masa Aktif Paket')
                        ->whereHas('customer', function ($query) use ($isMember) {
                            $query->where('customer_id', $isMember->id);
                        })
                        ->first();

                    if (!$checkBroadcast) {
                        $message = '<p>Halo <span>{{ customer_name }}</span> <br/> <span>Kami ingin memberitahu Anda bahwa paket ' . $mp->paket->name . ' Anda akan segera habis masa aktifnya dalam waktu ' . $daysDifference . ' hari lagi.<br/><br/>Segera lakukan pembayaran di menu <b>waiting payments</b> agar paket anda tetap aktif </span> <br/> <br/> <br/> Semangat <br/><br/>SoberMart.id</p>';

                        if ($this->createBroadcast($message, $isMember)) {
                            $token = bin2hex(random_bytes(75 / 2));

                            $checkPayment = Payment::where([
                                ['customer_id', $isMember->id],
                                ['order_id', $mp->id],
                                ['type_status', 'paket'],
                                ['status','pending']
                            ])
                            ->whereYear('created_at',Carbon::now()->year)
                            ->first();

                            if(!$checkPayment){
                                $create_payment = new Payment;
                                $create_payment->user_id = 0;
                                $create_payment->charge_id = $token;
                                $create_payment->bank = 'bca';
                                $create_payment->va_number = 1;
                                $create_payment->payment_channel = 'bank_transfer';
                                $create_payment->status = 'pending';
                                $create_payment->currency = 'IDR';
                                $create_payment->customer_type = 'Botble\Ecommerce\Models\Customer';
                                $create_payment->payment_type = 'confirm';
                                $create_payment->customer_id = $isMember->id;
                                $create_payment->amount = $mp->paket->nominal;
                                $create_payment->order_id = $mp->id;
                                $create_payment->type_status = 'paket';
                                $create_payment->expiry_time = Carbon::now()->addDay();
                                $create_payment->save();
                            }


                            // return $this->info($create_payment);
                        }
                    }
                    DB::commit();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->info($e->getMessage());
        }
    }

    private function createBroadcast($message, $member)
    {
        $new_broadcast = new Broadcast();
        $new_broadcast->title = 'Pengingat Masa Aktif Paket';
        $new_broadcast->description = $message;
        $new_broadcast->type = 'general';
        $new_broadcast->target = 'user';
        $new_broadcast->website = route('customer.payments');
        $new_broadcast->image = 'pngaaacom-1083181.png';
        if ($new_broadcast->save()) {
            $insert_customer = new BroadcastCustomer;
            $insert_customer->customer_id = $member->id;
            $insert_customer->broadcast_id = $new_broadcast->id;
            return $insert_customer->save();
        }
    }
}
