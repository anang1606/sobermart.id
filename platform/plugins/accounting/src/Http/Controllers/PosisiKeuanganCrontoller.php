<?php

namespace Botble\Accounting\Http\Controllers;

use Botble\Accounting\Models\PosisiKeuangan;
use Illuminate\Routing\Controller;

class PosisiKeuanganCrontoller extends Controller
{
    public function index()
    {
        page_title()->setTitle('Posisi Keuangan');

        return view('plugins/accounting::posisi-keuangan.index');
    }

    public function data()
    {
        $asset_lancar = PosisiKeuangan::where('urutan', 1)->orderBy('id_coa', 'ASC')->get();
        $asset_tidak_lancar = PosisiKeuangan::where('urutan', 2)->orderBy('id_coa', 'ASC')->get();
        $liabilitas = PosisiKeuangan::where('urutan', 4)->orderBy('id_coa', 'ASC')->get();
        $ekuitas = PosisiKeuangan::where('urutan', 5)->orderBy('id_coa', 'ASC')->get();

        $data['asset_lancar'] = $asset_lancar;
        $data['asset_tidak_lancar'] = $asset_tidak_lancar;
        $data['liabilitas'] = $liabilitas;
        $data['ekuitas'] = $ekuitas;
        return view('plugins/accounting::posisi-keuangan.data', $data);
    }
}
