<?php

namespace Botble\Payment\Http\Controllers;
use Illuminate\Routing\Controller;
use Botble\Payment\Models\Level;
use Botble\Ecommerce\Models\PaketMaster;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Payment\Tables\LevelTable;
use Botble\Marketplace\Models\Revenue;
use MarketplaceHelper;

class AccumulationController extends Controller{

    public function index(LevelTable $table){
        page_title()->setTitle('Akumulasi');

        Level::truncate();

        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        $paket_masters = PaketMaster::get();
        foreach ($paket_masters as $paket_master) {
            $level = new Level;
            $members = MemberPaket::where('id_paket',$paket_master->id)->with('customer')->get();
            $get_member = MemberPaket::withSum('paket','nominal')->where('id_paket',$paket_master->id)->first();

            $get_totalbelanja = '';
            foreach ($members as $key => $member) {
                $get_totalbelanja = Revenue::selectRaw('SUM(amount) as total, SUM(fee) as feeTotal')
                ->where('id_paket', $member->id)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->first();
            }

            if($get_member){
                $level->total_member_pack = $paket_master->nominal;
                $level->total_member = count($members);
                $level->belanja_per_bulan = ($get_totalbelanja) &&  $get_totalbelanja->total;
                $level->belanja_pribadi = ($get_totalbelanja) &&  $get_totalbelanja->total * ((int)MarketplaceHelper::getSettingKey('pendapatan_pribadi')/100);
                $level->assumsition_perusahaan =  ($get_totalbelanja) &&  $get_totalbelanja->feeTotal;
                $level->profit_assumption = $get_member->nominal;
                $level->pendapatan_dari_adm = ($paket_master->nominal * count($members)) - $get_member->paket_sum_nominal;

                $level->save();
            }
            // return $level;
        }
        // return $paket_masters;

        return $table->renderTable();
    }
}
