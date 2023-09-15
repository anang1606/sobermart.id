<?php

namespace Botble\Accounting\Http\Controllers;

use Botble\Accounting\Models\AccNeracaLajur;
use Illuminate\Routing\Controller;
use Assets;
use Botble\Accounting\Models\Coa;
use Botble\Accounting\Models\CoaSaldo;
use DB;
use Illuminate\Http\Request;

class NeracaLajurController extends Controller
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

        AccNeracaLajur::on(\Config::get('db'))
            ->whereNotNull('idcoa')
            ->delete();

        $data['tahun']  = $tahun;
        $data['coa']    = Coa::on(\Config::get('db'))->where('typecoa', 'NOT LIKE', 'Level%')->orderby('idcoa')->get();
        return view('plugins/accounting::neraca-lajur.index', $data);
    }

    public function view(string $prefix, Request $request)
    {
        if ($prefix === 'tanggal') {
            return $this->_getDataHarian($request);
        } else if ($prefix === 'bulan') {
            return $this->_getDataBulanan($request);
        } else if ($prefix === 'tahun') {
            return $this->_getDataTahunan($request);
        }
    }

    public function _getDataHarian(Request $request)
    {
        $hari = $request->date;
        //$akun = $s[1];

        $date = date_create($hari);
        $rangeFilter = "Tanggal " . date_format($date, "d F Y");

        if ($hari == '') return '404';

        $tahun = date_format($date, "Y");

        AccNeracaLajur::on(\Config::get('db'))
            ->whereNotNull('idcoa')
            ->delete();

        $level1  = Coa::on(\Config::get('db'))
            ->where('typecoa', '=', 'Level 1')
            ->get();

        foreach ($level1 as $row1) {
            $totalakun = DB::table('coa_jurnal')
                ->select(DB::raw('sum(debit+kredit) as total'))
                ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                ->where('idcoa', 'Like', substr($row1->idcoa, 0, 1) . "%")
                ->first()->total;
            if ($totalakun > 0) {
                $select = Coa::on(\Config::get('db'))
                    ->select('idcoa', 'namacoa', 'typecoa')
                    ->where('typecoa', 'LIKE', 'Level%')
                    ->where('idcoa', 'LIKE', substr($row1->idcoa, 0, 1) . '%')
                    ->orderby('idcoa');

                $rows_inserted = AccNeracaLajur::on(\Config::get('db'))
                    ->insertUsing(['idcoa', 'namacoa', 'typecoa'], $select);

                $dataisi = DB::table('coa_jurnal')
                    ->select(
                        'coa_jurnal.idcoa',
                        'a1.namacoa',
                        'a1.typecoa'
                    )
                    ->join('coas as a1', 'coa_jurnal.idcoa', '=', 'a1.idcoa')
                    ->where(DB::raw("(DATE_FORMAT(coa_jurnal.tanggal,'%Y'))"), $tahun)
                    ->where('a1.typecoa', 'NOT LIKE', 'Level%')
                    ->where('coa_jurnal.idcoa', 'LIKE', substr($row1->idcoa, 0, 1) . '%')
                    ->orderby('coa_jurnal.idcoa')
                    ->get()
                    ->unique();

                foreach ($dataisi as $row2) {
                    $saldo  = CoaSaldo::on(\Config::get('db'))
                        ->where('idcoa', '=', $row2->idcoa)
                        ->first();
                    if ($saldo == true) {
                        $adebit = $saldo->debit;
                        $akredit = $saldo->kredit;
                    } else {
                        $adebit = 0;
                        $akredit = 0;
                    }


                    $gerakan = DB::table('coa_jurnal')
                        ->select(DB::raw('sum(debit) as bdebit'), DB::raw('sum(kredit) as bkredit'))
                        ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                        ->where('idcoa', '=', $row2->idcoa)
                        ->first();

                    if ($gerakan == true) {
                        $bdebit = $gerakan->bdebit;
                        $bkredit = $gerakan->bkredit;
                    } else {
                        $bdebit = 0;
                        $bkredit = 0;
                    }

                    $dtlevel1 = new AccNeracaLajur();
                    $dtlevel1->setConnection(\Config::get('db'));
                    $dtlevel1->idcoa    = $row2->idcoa;
                    $dtlevel1->namacoa   = $row2->namacoa;
                    $dtlevel1->typecoa  = $row2->typecoa;
                    $dtlevel1->adebit  = $adebit;
                    $dtlevel1->akredit  = $akredit;
                    $dtlevel1->bdebit  = $bdebit;
                    $dtlevel1->bkredit  = $bkredit;
                    $dtlevel1->cdebit  = $adebit + $bdebit;
                    $dtlevel1->ckredit  = $akredit + $bkredit;
                    if (substr($row1->idcoa, 0, 1) == '1') {
                        $dtlevel1->edebit  = $adebit + $bdebit;
                        $dtlevel1->ekredit  = $akredit + $bkredit;
                    }
                    $dtlevel1->save();
                }
            } else {
                $dtlevel1 = new AccNeracaLajur();
                $dtlevel1->setConnection(\Config::get('db'));
                $dtlevel1->idcoa    = $row1->idcoa;
                $dtlevel1->namacoa   = $row1->namacoa;
                $dtlevel1->typecoa  = $row1->typecoa;
                $dtlevel1->save();
            }
        }

        $total1 = DB::table('acc_neracalajur')
            ->select(
                DB::raw('sum(adebit) as tadebit'),
                DB::raw('sum(akredit) as takredit'),
                DB::raw('sum(bdebit) as tbdebit'),
                DB::raw('sum(bkredit) as tbkredit'),
                DB::raw('sum(cdebit) as tcdebit'),
                DB::raw('sum(ckredit) as tckredit'),
                DB::raw('sum(ddebit) as tddebit'),
                DB::raw('sum(dkredit) as tdkredit'),
                DB::raw('sum(edebit) as tedebit'),
                DB::raw('sum(ekredit) as tekredit')
            )
            ->first();
        $neraca  = AccNeracaLajur::on(\Config::get('db'))->orderby('idcoa')->get();

        return view('plugins/accounting::neraca-lajur.view', compact('rangeFilter', 'neraca', 'rangeFilter', 'total1'));
    }

    public function _getDataBulanan(Request $request)
    {
        $bulan = $request->tahun . '-' . $request->bulan;
        //$akun = $s[1];

        $date = date_create($bulan . '-01');

        if ($bulan == '') return '404';

        $rangeFilter = "Bulan " . date_format($date, "F Y");
        $tahun = date_format($date, "Y");


        AccNeracaLajur::on(\Config::get('db'))
            ->whereNotNull('idcoa')
            ->delete();

        $level1  = Coa::on(\Config::get('db'))
            ->where('typecoa', '=', 'Level 1')
            ->get();
        //dd($level1);

        foreach ($level1 as $row1) {
            $totalakun = DB::table('coa_jurnal')
                ->select(DB::raw('sum(debit+kredit) as total'))
                ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                ->where('idcoa', 'Like', substr($row1->idcoa, 0, 1) . "%")
                ->first()->total;
            if ($totalakun > 0) {
                $select = Coa::on(\Config::get('db'))
                    ->select('idcoa', 'namacoa', 'typecoa')
                    ->where('typecoa', 'LIKE', 'Level%')
                    ->where('idcoa', 'LIKE', substr($row1->idcoa, 0, 1) . '%')
                    ->orderby('idcoa');

                $rows_inserted = AccNeracaLajur::on(\Config::get('db'))
                    ->insertUsing(['idcoa', 'namacoa', 'typecoa'], $select);

                $dataisi = DB::table('coa_jurnal')
                    ->select(
                        'coa_jurnal.idcoa',
                        'a1.namacoa',
                        'a1.typecoa',
                        DB::raw('sum(coa_jurnal.debit) as tdebit'),
                        DB::raw('sum(coa_jurnal.kredit) as tkredit')
                    )
                    ->join('coas as a1', 'coa_jurnal.idcoa', '=', 'a1.idcoa')
                    ->where(DB::raw("(DATE_FORMAT(coa_jurnal.tanggal,'%Y'))"), $tahun)
                    ->where('a1.typecoa', 'NOT LIKE', 'Level%')
                    ->where('coa_jurnal.idcoa', 'LIKE', substr($row1->idcoa, 0, 1) . '%')
                    ->groupby('coa_jurnal.idcoa')
                    ->orderby('coa_jurnal.idcoa')
                    ->get();

                foreach ($dataisi as $row2) {
                    $saldo  = CoaSaldo::on(\Config::get('db'))
                        ->where('idcoa', '=', $row2->idcoa)
                        ->first();
                    if ($saldo == true) {
                        $adebit = $saldo->debit;
                        $akredit = $saldo->kredit;
                    } else {
                        $adebit = 0;
                        $akredit = 0;
                    }


                    $gerakan = DB::table('coa_jurnal')
                        ->select(DB::raw('sum(debit) as bdebit'), DB::raw('sum(kredit) as bkredit'))
                        ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                        ->where('idcoa', '=', $row2->idcoa)
                        ->first();

                    if ($gerakan == true) {
                        $bdebit = $gerakan->bdebit;
                        $bkredit = $gerakan->bkredit;
                    } else {
                        $bdebit = 0;
                        $bkredit = 0;
                    }


                    $dtlevel1 = new AccNeracaLajur();
                    $dtlevel1->setConnection(\Config::get('db'));
                    $dtlevel1->idcoa    = $row2->idcoa;
                    $dtlevel1->namacoa   = $row2->namacoa;
                    $dtlevel1->typecoa  = $row2->typecoa;
                    $dtlevel1->adebit  = $adebit;
                    $dtlevel1->akredit  = $akredit;
                    $dtlevel1->bdebit  = $bdebit;
                    $dtlevel1->bkredit  = $bkredit;
                    $dtlevel1->cdebit  = $adebit + $bdebit;
                    $dtlevel1->ckredit  = $akredit + $bkredit;
                    if (substr($row1->idcoa, 0, 1) == '1') {
                        $dtlevel1->edebit  = $adebit + $bdebit;
                        $dtlevel1->ekredit  = $akredit + $bkredit;
                    }
                    $dtlevel1->save();
                }
            } else {
                $dtlevel1 = new AccNeracaLajur();
                $dtlevel1->setConnection(\Config::get('db'));
                $dtlevel1->idcoa    = $row1->idcoa;
                $dtlevel1->namacoa   = $row1->namacoa;
                $dtlevel1->typecoa  = $row1->typecoa;
                $dtlevel1->save();
            }
        }

        $total1 = DB::table('acc_neracalajur')
            ->select(
                DB::raw('sum(adebit) as tadebit'),
                DB::raw('sum(akredit) as takredit'),
                DB::raw('sum(bdebit) as tbdebit'),
                DB::raw('sum(bkredit) as tbkredit'),
                DB::raw('sum(cdebit) as tcdebit'),
                DB::raw('sum(ckredit) as tckredit'),
                DB::raw('sum(ddebit) as tddebit'),
                DB::raw('sum(dkredit) as tdkredit'),
                DB::raw('sum(edebit) as tedebit'),
                DB::raw('sum(ekredit) as tekredit')
            )
            ->first();
        $neraca  = AccNeracaLajur::on(\Config::get('db'))->orderby('idcoa')->get();
        return view('plugins/accounting::neraca-lajur.view', compact('rangeFilter', 'neraca', 'rangeFilter', 'total1'));
    }

    public function _getDataTahunan(Request $request)
    {
        $tahun = $request->tahun;

        if ($tahun == '') return '404';
        $rangeFilter = 'Tahun ' . $tahun;

        AccNeracaLajur::on(\Config::get('db'))
            ->whereNotNull('idcoa')
            ->delete();

        $level1  = Coa::on(\Config::get('db'))
            ->where('typecoa', '=', 'Level 1')
            ->get();


        foreach ($level1 as $row1) {
            $totalakun = DB::table('coa_jurnal')
                ->select(DB::raw('sum(debit+kredit) as total'))
                ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                ->where('idcoa', 'Like', substr($row1->idcoa, 0, 1) . "%")
                ->first()->total;

            if ($totalakun > 0) {
                $select = Coa::on(\Config::get('db'))
                    ->select('idcoa', 'namacoa', 'typecoa')
                    ->where('typecoa', 'LIKE', 'Level%')
                    ->where('idcoa', 'LIKE', substr($row1->idcoa, 0, 1) . '%')
                    ->orderby('idcoa');

                $rows_inserted = AccNeracaLajur::on(\Config::get('db'))
                    ->insertUsing(['idcoa', 'namacoa', 'typecoa'], $select);

                $dataisi = DB::table('coa_jurnal')
                    ->select(
                        'coa_jurnal.idcoa',
                        'a1.namacoa',
                        'a1.typecoa'
                    )
                    ->join('coas as a1', 'coa_jurnal.idcoa', '=', 'a1.idcoa')
                    ->where(DB::raw("(DATE_FORMAT(coa_jurnal.tanggal,'%Y'))"), $tahun)
                    ->where('a1.typecoa', 'NOT LIKE', 'Level%')
                    ->where('coa_jurnal.idcoa', 'LIKE', substr($row1->idcoa, 0, 1) . '%')
                    ->orderby('coa_jurnal.idcoa')
                    ->get()
                    ->unique();

                foreach ($dataisi as $row2) {
                    $saldo  = CoaSaldo::on(\Config::get('db'))
                        ->where('idcoa', '=', $row2->idcoa)
                        ->first();
                    if ($saldo == true) {
                        $adebit = $saldo->debit;
                        $akredit = $saldo->kredit;
                    } else {
                        $adebit = 0;
                        $akredit = 0;
                    }


                    $gerakan = DB::table('coa_jurnal')
                        ->select(DB::raw('sum(debit) as bdebit'), DB::raw('sum(kredit) as bkredit'))
                        ->where(DB::raw("(DATE_FORMAT(tanggal,'%Y'))"), $tahun)
                        ->where('idcoa', '=', $row2->idcoa)
                        ->first();

                    if ($gerakan == true) {
                        $bdebit = $gerakan->bdebit;
                        $bkredit = $gerakan->bkredit;
                    } else {
                        $bdebit = 0;
                        $bkredit = 0;
                    }

                    $dtlevel1 = new AccNeracaLajur();
                    $dtlevel1->setConnection(\Config::get('db'));
                    $dtlevel1->idcoa    = $row2->idcoa;
                    $dtlevel1->namacoa   = $row2->namacoa;
                    $dtlevel1->typecoa  = $row2->typecoa;
                    $dtlevel1->adebit  = $adebit;
                    $dtlevel1->akredit  = $akredit;
                    $dtlevel1->bdebit  = $bdebit;
                    $dtlevel1->bkredit  = $bkredit;
                    $dtlevel1->cdebit  = $adebit + $bdebit;
                    $dtlevel1->ckredit  = $akredit + $bkredit;
                    if (substr($row1->idcoa, 0, 1) == '1') {
                        $dtlevel1->edebit  = $adebit + $bdebit;
                        $dtlevel1->ekredit  = $akredit + $bkredit;
                    }
                    $dtlevel1->save();
                }
            } else {
                $dtlevel1 = new AccNeracaLajur();
                $dtlevel1->setConnection(\Config::get('db'));
                $dtlevel1->idcoa    = $row1->idcoa;
                $dtlevel1->namacoa   = $row1->namacoa;
                $dtlevel1->typecoa  = $row1->typecoa;
                $dtlevel1->save();
            }
        }

        $total1 = DB::table('acc_neracalajur')
            ->select(
                DB::raw('sum(adebit) as tadebit'),
                DB::raw('sum(akredit) as takredit'),
                DB::raw('sum(bdebit) as tbdebit'),
                DB::raw('sum(bkredit) as tbkredit'),
                DB::raw('sum(cdebit) as tcdebit'),
                DB::raw('sum(ckredit) as tckredit'),
                DB::raw('sum(ddebit) as tddebit'),
                DB::raw('sum(dkredit) as tdkredit'),
                DB::raw('sum(edebit) as tedebit'),
                DB::raw('sum(ekredit) as tekredit')
            )
            ->first();
        //$tadebit//dd($total1);

        $neraca  = AccNeracaLajur::on(\Config::get('db'))->orderby('idcoa')->get();
        return view('plugins/accounting::neraca-lajur.view', compact('rangeFilter', 'neraca', 'rangeFilter', 'total1'));
    }
}
