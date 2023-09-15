<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\BroadcastCustomer;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class DeactiveMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactive:member';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for deactive member if not pay 2 weeks';

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

                $daysDifference = $currentTime->diffInDays($expireDate);

                if ($expireDate->isBefore($currentTime)) {
                    if ($daysDifference >= 14) {
                        $isMember = Customer::where([['id', $mp->user_id]])->first();
                        if ($isMember) {
                            $getPayment = Payment::where([
                                ['customer_id', $isMember->id],
                                ['order_id', $mp->id],
                                ['status', PaymentStatusEnum::PENDING],
                            ])->first();
                            if ($getPayment) {
                                $getPayment->status = PaymentStatusEnum::FAILED;
                                if ($getPayment->save()) {
                                    Customer::where('parent', $isMember->id)
                                    ->whereHas('paket',function($query) use($mp) {
                                        $query->where('id_paket',$mp->id_paket);
                                    })
                                    ->update([
                                        'parent' => NULL
                                    ]);
                                    if($mp->delete()){
                                        if((int)$mp->is_active === 1){
                                            $changeActive = MemberPaket::where([
                                                ['user_id',$mp->user_id]
                                            ])->first();

                                            if($changeActive){
                                                $changeActive->is_active = 1;
                                                $changeActive->save();
                                            }

                                            $mp->is_active = 0;
                                        }
                                        $message = '<p>Halo <span>{{ customer_name }}</span> <br/> <span>Kami ingin memberitahukan Anda bahwa paket ' . $mp->paket->name . ' anda sudah habis masa aktifnya.<br/><br/>Lakukan pembelian di menu <b>Paket Member</b> jika anda ingin membeli paket yang sama. </span> <br/> <br/> <br/> Semangat <br/><br/>SoberMart.id</p>';
                                        $this->createBroadcast($message, $isMember);
                                    }
                                    DB::commit();
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->info($e->getMessage());
        }
    }

    private function createBroadcast($message, $member)
    {
        $new_broadcast = new Broadcast;
        $new_broadcast->title = 'Masa Aktif Paket';
        $new_broadcast->description = $message;
        $new_broadcast->type = 'general';
        $new_broadcast->target = 'user';
        $new_broadcast->website = route('customer.payments');
        $new_broadcast->image = 'pngaaacom-1083181.png';
        if ($new_broadcast->save()) {
            $insert_customer = new BroadcastCustomer();
            $insert_customer->customer_id = $member->id;
            $insert_customer->broadcast_id = $new_broadcast->id;
            return $insert_customer->save();
        }
    }
}
