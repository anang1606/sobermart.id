<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\BroadcastCustomer;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class NotifTargetMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif-target-paket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'handle notification target paket member';

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
                $isMember = Customer::where([['id', $mp->user_id], ['parent', '!=', null]])->first();

                if ($isMember) {
                    $joinDate = Carbon::parse($mp->created_at);
                    $expireDateDB = Carbon::parse($mp->expire_date);
                    $currentMonth = Carbon::now()->month;
                    $currentYear = Carbon::now()->year;

                    $firstJoin = false;
                    if ($joinDate->month === $currentMonth && $joinDate->year === $currentYear) {
                        $expireDate = Carbon::create(Carbon::now()->year, Carbon::now()->month, $expireDateDB->day)->addMonth();
                        $firstJoin = true;
                    } else {
                        $expireDate = Carbon::create(Carbon::now()->year, Carbon::now()->month, $expireDateDB->day);
                    }
                    $daysDifference = $expireDate->diffInDays(Carbon::now());

                    if ($daysDifference <= 10 && $expireDate->isAfter(Carbon::now()) && $firstJoin === false) {

                        $joinDate = Carbon::parse($mp->expire_date);
                        $carbonNow = Carbon::now();
                        $currentMonthBuy = Carbon::now()->month;
                        $currentYearBuy = Carbon::now()->year;
                        if ($joinDate->month !== $currentMonthBuy || $joinDate->year !== $currentYearBuy) {
                            // Tanggal kedaluwarsa bukan dalam bulan dan tahun saat ini
                            $previousDate = Carbon::create($carbonNow->year, $carbonNow->subMonth()->month, $joinDate->day);
                            $nextDate = Carbon::create($carbonNow->year, Carbon::now()->month, $joinDate->addDay()->day);
                        } else {
                            $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $joinDate->subDay()->day);
                            $nextDate = Carbon::create($carbonNow->year, Carbon::now()->addMonth()->month, $joinDate->addDay()->day);
                        }

                        $totalBelanja = Order::where([
                            ['user_id', $isMember->id],
                            ['id_paket', $mp->id_paket],
                            ['status', '<>', OrderStatusEnum::CANCELED()],
                            ['status', '<>', OrderStatusEnum::RETURNED()]
                        ])
                            ->whereHas('payment', function ($query) {
                                $query->where('status', PaymentStatusEnum::COMPLETED());
                            })
                            ->whereDate('created_at', '<', $nextDate)
                            ->whereDate('created_at', '>', $previousDate)
                            ->selectRaw('SUM(amount - shipping_amount) as total_amount')
                            ->first()
                            ->total_amount;

                        $totalBelanja = ($totalBelanja) ? $totalBelanja : 0;
                        if ((int)$totalBelanja < (int)$mp->paket->nominal) {
                            $checkBroadcast = Broadcast::where('title', 'Pengingat Target Member')
                                ->whereHas('customer', function ($query) use ($isMember) {
                                    $query->where('customer_id', $isMember->id);
                                })
                                ->first();

                            if (!$checkBroadcast) {
                                $message = '<p>Halo <span>{{ customer_name }}</span> <br/> <span>Sisa waktu untuk paket ' . $mp->paket->name . ' tinggal ' . $daysDifference . ' hari sebelum batas akhir! Kami ingin mengingatkan Anda bahwa target pembelian individu Anda masih belum tercapai. Ini adalah peluang terakhir untuk menggapai target dan meraih keberhasilan.</span> <br/> <br/> <br/> Semangat <br/><br/>SoberMart.id</p>';

                                if($this->createBroadcast($message,$isMember)){
                                    $getParent = Customer::find($isMember->parent);
                                    if($getParent){
                                        $messageParent = '<p>Halo <span>{{ customer_name }}</span> <br/> <span>Sisa waktu untuk paket ' . $mp->paket->name . ' tinggal ' . $daysDifference . ' hari sebelum batas akhir! Kami ingin mengingatkan Anda bahwa target pembelian member Anda atas nama '.$isMember->name.' masih belum tercapai. Ini adalah peluang terakhir untuk menggapai target dan meraih keberhasilan.</span> <br/> <br/> <br/> Semangat <br/><br/>SoberMart.id</p>';
                                    }
                                    $this->createBroadcast($messageParent,$getParent);
                                }
                            }
                            // return $this->info($checkBroadcast);
                        }
                        DB::commit();
                        // $this->info($mp);
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->info($e->getMessage());
        }
    }

    private function createBroadcast($message,$member)
    {
        $new_broadcast = new Broadcast();
        $new_broadcast->title = 'Pengingat Target Member';
        $new_broadcast->description = $message;
        $new_broadcast->type = 'general';
        $new_broadcast->target = 'user';
        $new_broadcast->website = route('customer.member-list');
        $new_broadcast->image = 'pngaaacom-1083181.png';
        if ($new_broadcast->save()) {
            $insert_customer = new BroadcastCustomer;
            $insert_customer->customer_id = $member->id;
            $insert_customer->broadcast_id = $new_broadcast->id;
            return $insert_customer->save();
        }
    }
}
