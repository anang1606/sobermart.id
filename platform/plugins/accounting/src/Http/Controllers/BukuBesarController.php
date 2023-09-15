<?php

namespace Botble\Accounting\Http\Controllers;

use Illuminate\Routing\Controller;
use Assets;
use Botble\Accounting\Models\Coa;
use DB;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index()
    {
        page_title()->setTitle('Buku Besar');

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
        $data['coa']    = Coa::on(\Config::get('db'))->where('typecoa', 'NOT LIKE', 'Level%')->orderby('idcoa')->get();
        return view('plugins/accounting::buku-besar.index', $data);
    }

    public function view(string $prefix, Request $request)
    {
        if ($prefix === 'tanggal') {
            return $this->_getDataHarian($request);
        } else if ($prefix === 'bulan') {
            return $this->_getDataBulanan($request);
        }else if ($prefix === 'tahun') {
            return $this->_getDataTahunan($request);
        }
    }

    protected function _getDataHarian($request)
    {
        $hari = $request->date;
        $akun = $request->akun;
        if (!$akun) {
            return redirect()->route('buku-besar.index')->with('information', 'Pengguna Wajib Memilih Akun');
        }

        $date = date_create($hari);

        if ($hari == '') return '404';

        $rangeFilter = "Tanggal " . date_format($date, 'd F Y');
        $tahun = date_format($date, "Y");

        $saldotahun = DB::table('coa_saldos')
            ->select(DB::raw('sum(debit - kredit) as saldo'))
            ->where('tahun', '=', $tahun)
            ->where('idcoa', '=', $akun)
            ->first()->saldo;

        if (date_format($date, "d-m") != "01-01") {
            $prevdebit = DB::table('coa_jurnals')
                ->select(DB::raw('sum(debit) as total'))
                ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                ->where('tanggal', '<', $date)
                ->where('idcoa', '=', $akun)
                ->first()->total;

            $prevkredit = DB::table('coa_jurnals')
                ->select(DB::raw('sum(kredit) as total'))
                ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                ->where('tanggal', '<', $date)
                ->where('idcoa', '=', $akun)
                ->first()->total;
        } else {
            $prevdebit = 0;
            $prevkredit = 0;
        }

        $saldoawal = ($saldotahun + $prevdebit) - $prevkredit;

        $acc = $this->myQuery('Harian', $saldoawal, date_format($date, 'Y/m/d'), $akun, $saldoakhir);
        if (strlen($akun) > 0)
            $coaname    = Coa::where('idcoa', '=', $akun)->first()->namacoa;
        else
            $coaname    = '';

        return view('plugins/accounting::buku-besar.view', compact('acc', 'rangeFilter', 'akun', 'coaname', 'saldoawal', 'saldoakhir'));
    }

    public function _getDataBulanan(Request $request)
    {
        $bulan = $request->tahun . '-' . $request->bulan;
        $akun = $request->akun;
        if (!$akun) {
            return redirect()->route('buku-besar.index')->with('information', 'Pengguna Wajib Memilih Akun');
        }

        $date = date_create($bulan . '-01');
        if ($bulan == '') return '404';

        $rangeFilter = date_format($date, "F Y");
        $tahun = date_format($date, "Y");

        $saldotahun = DB::table('coa_saldos')
            ->select(DB::raw('sum(debit - kredit) as saldo'))
            ->where('tahun', '=', $tahun)
            ->where('idcoa', '=', $akun)
            ->first()->saldo;
        if (date_format($date, "m") != "01") {
            $prevdebit = DB::table('coa_jurnals')
                ->select(DB::raw('sum(debit) as total'))
                ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                ->where('tanggal', '<', $date)
                ->where('idcoa', '=', $akun)
                ->first()->total;

            $prevkredit = DB::table('coa_jurnals')
                ->select(DB::raw('sum(kredit) as total'))
                ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                ->where('tanggal', '<', $date)
                ->where('idcoa', '=', $akun)
                ->first()->total;
        } else {
            $prevdebit = 0;
            $prevkredit = 0;
        }

        $saldoawal = ($saldotahun + $prevdebit) - $prevkredit;

        $acc = $this->myQuery('Bulanan', $saldoawal, $bulan, $akun, $saldoakhir);

        if (strlen($akun) > 0)
            $coaname    = Coa::where('idcoa', '=', $akun)->first()->namacoa;
        else
            $coaname    = '';

        return view('plugins/accounting::buku-besar.view', compact('acc', 'rangeFilter', 'akun', 'coaname', 'saldoawal', 'saldoakhir'));
    }

    public function _getDataTahunan(Request $request)
    {
        $tahun = $request->tahun;
        $akun = $request->akun;
        if ($akun == '') {
            return redirect()->route('buku-besar.index')->with('information', 'Pengguna Wajib Memilih Akun');
        }

        if ($tahun == '') return '404';

        $saldoawal = DB::table('coa_saldos')
            ->select(DB::raw('sum(debit - kredit) as saldo'))
            ->where('tahun', '=', $tahun)
            ->where('idcoa', '=', $akun)
            ->first()->saldo;

        $datepicker = true;
        $acc = $this->myQuery('Tahunan', $saldoawal, $tahun, $akun, $saldoakhir);
        $rangeFilter = $tahun;
        if (strlen($akun) > 0)
            $coaname    = Coa::on(\Config::get('db'))->where('idcoa', '=', $akun)->first()->namacoa;
        else
            $coaname    = '';

        return view('plugins/accounting::buku-besar.view', compact('acc', 'rangeFilter', 'akun', 'coaname', 'saldoawal', 'saldoakhir'));
    }

    public function myQuery($jnsdata, $vsaldo, $vdata, $vakun, &$vsaldoakhir)
    {
        if (strlen($vakun) > 0)
            $where1 = "idcoa = '$vakun'";
        else
            $where1 = "idcoa IS NOT NULL";

        if ($jnsdata == 'Harian') {
            $where2 = "tanggal = '$vdata'";
        } elseif ($jnsdata == 'Bulanan') {
            $where2 = "DATE_FORMAT(tanggal, '%Y-%m') = '$vdata'";
        } elseif ($jnsdata == 'Tahunan') {
            $where2 = "YEAR(tanggal) = " . $vdata;
        }

        $data = DB::select("SELECT tanggal, kode_reff, keterangan, debit, kredit,
		@saldo := @saldo + debit - kredit AS saldo
		FROM coa_jurnals, (SELECT @saldo := $vsaldo) AS variableInit
		WHERE $where1 AND $where2 ORDER BY id,tanggal");

        $data2 = DB::select("SELECT @saldo := @saldo + debit - kredit AS saldo
		FROM coa_jurnals, (SELECT @saldo := $vsaldo) AS variableInit
		WHERE $where1 AND $where2 ORDER BY id desc,tanggal desc Limit 1");

        if ($data2 == true)
            $vsaldoakhir = $data2[0]->saldo;
        else
            $vsaldoakhir = 0;

        return $data;
    }
}
