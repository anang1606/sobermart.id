<?php

namespace Botble\Accounting\Http\Controllers;

use Illuminate\Routing\Controller;
use Assets;
use Botble\Accounting\Models\Coa;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Models\Shipment;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class OngkosKirimController extends Controller
{
    public function index()
    {
        page_title()->setTitle('Laporan Ongkos Kirim');

        Assets::addScriptsDirectly('https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js');

        $thnnow = date('Y');
        $thn = 2020;
        $tahun = [];
        for ($i = 0; $i < 100; $i++) {
            $thn = $thn + 1;
            array_push($tahun, ($thn));
            if ($thn >= $thnnow) break;
        }

        $data['tahun']      = $tahun;
        $data['courier'] = ['jne', 'j&t'];
        return view('plugins/accounting::ongkos-kirim.index', $data);
    }

    public function view(string $prefix, Request $request)
    {
        if ($prefix === 'tanggal') {
            return $this->_getDataHarian($request);
        } else if ($prefix === 'bulan') {
            return $this->_getDataBulanan($request);
        } else if ($prefix === 'tahun') {
            return $this->_getDataTahunan($request);
        }else if ($prefix === 'range') {
            return $this->_getDataRange($request);
        }
    }

    protected function _getDataHarian($request)
    {
        $hari = $request->date;
        $date = date_create($hari);

        if ($hari == '') return '404';

        $rangeFilter = "Tanggal " . date_format($date, 'd F Y');

        $totalPengirimans = Shipment::select(DB::raw("LEFT(shipping_company_name, 3) AS short_provider"), DB::raw('SUM(price) as total_price'))
            ->where([
                ['status',ShippingStatusEnum::DELIVERED]
            ])
            ->whereNotNull('shipment_id')
            ->whereDate('created_at',$date)
            ->groupBy('short_provider')
            ->get();

        return view('plugins/accounting::ongkos-kirim.view', compact('totalPengirimans','rangeFilter'));
    }

    public function _getDataBulanan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if ($bulan == '') return '404';

        $date = Carbon::create($tahun,$bulan,1);
        $rangeFilter = date_format($date, "F Y");;

        $totalPengirimans = Shipment::select(DB::raw("LEFT(shipping_company_name, 3) AS short_provider"), DB::raw('SUM(price) as total_price'))
            ->where([
                ['status',ShippingStatusEnum::DELIVERED]
            ])
            ->whereNotNull('shipment_id')
            ->whereMonth('created_at',$bulan)
            ->whereYear('created_at',$tahun)
            ->groupBy('short_provider')
            ->get();

        return view('plugins/accounting::ongkos-kirim.view', compact('totalPengirimans','rangeFilter'));
    }

    public function _getDataTahunan(Request $request)
    {
        $tahun = $request->tahun;

        if ($tahun == '') return '404';

        $rangeFilter = $tahun;
        $totalPengirimans = Shipment::select(DB::raw("LEFT(shipping_company_name, 3) AS short_provider"), DB::raw('SUM(price) as total_price'))
            ->where([
                ['status',ShippingStatusEnum::DELIVERED]
            ])
            ->whereNotNull('shipment_id')
            ->whereYear('created_at',$tahun)
            ->groupBy('short_provider')
            ->get();

        return view('plugins/accounting::ongkos-kirim.view', compact('totalPengirimans','rangeFilter'));
    }

    public function _getDataRange(Request $request)
    {
        $date1 = date_create($request->date1);
        $date2 = date_create($request->date2);

        if ($date1 == '') return '404';

        $rangeFilter = "Tanggal " . date_format($date1, 'd F Y')." - " . date_format($date2, 'd F Y');

        $totalPengirimans = Shipment::select(DB::raw("LEFT(shipping_company_name, 3) AS short_provider"), DB::raw('SUM(price) as total_price'))
            ->where([
                ['status',ShippingStatusEnum::DELIVERED]
            ])
            ->whereNotNull('shipment_id')
            ->whereDate('created_at','>=',$date1)
            ->whereDate('created_at','<=',$date2)
            ->groupBy('short_provider')
            ->get();

        return view('plugins/accounting::ongkos-kirim.view', compact('totalPengirimans','rangeFilter'));
    }
}
