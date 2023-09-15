<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Marketplace\Repositories\Eloquent\AhliWarisRepository;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use RvMedia;
use Yajra\DataTables\DataTables;

class AhliWarisTable extends TableAbstract
{
    protected $hasActions = false;
    protected $hasOperations = false;

    protected $hasFilter = false;
    protected $hasCheckbox = false;

    protected $rowNumber = 1;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AhliWarisRepository $storeRepository)
    {
        $this->repository = $storeRepository;
        parent::__construct($table, $urlGenerator);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->addColumn('row_number', function ($item) use (&$rowNumber) {
                $rowNumber++; // Tambahkan nomor baris setiap kali fungsi dipanggil
                return $rowNumber; // Kembalikan nomor baris
            })
            ->editColumn('customer',function($item){
                return $item->customer->name;
            })
            ->editColumn('nik',function($item){
                return decrypt_data_ahli_waris($item->nik, base64_decode($item->uuid));
            })
            ->editColumn('alamat_ktp',function($item){
                $decryptAlamat = json_decode(decrypt_data_ahli_waris($item->alamat_ktp, base64_decode($item->uuid)));
                $alamat = $decryptAlamat->alamat_ktp .', '.$decryptAlamat->kecamatan_ktp.', '.$decryptAlamat->kota_ktp.', '.$decryptAlamat->provinsi_ktp;
                return $alamat;
            })
            ->editColumn('alamat_tinggal',function($item){
                $decryptAlamat = json_decode(decrypt_data_ahli_waris($item->alamat_tinggal, base64_decode($item->uuid)));
                $alamat = $decryptAlamat->alamat_tinggal .', '.$decryptAlamat->kecamatan_tinggal.', '.$decryptAlamat->kota_tinggal.', '.$decryptAlamat->provinsi_tinggal;
                return $alamat;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select('*')->with(['customer'])
            ->orderBy('customer_id','ASC');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'row_number' => [
                'title' => 'No',
                'width' => '20px',
            ],
            'customer' => [
                'title' => 'Nama Customer',
                'width' => '120px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'width' => '120px',
            ],
            'nik' => [
                'title' => 'NIK KTP',
                'class' => 'text-start',
            ],
            'alamat_ktp' => [
                'title' => 'Alamat KTP',
                'class' => 'text-start',
            ],
            'alamat_tinggal' => [
                'title' => 'Alamat Tempat Tinggal',
                'class' => 'text-start',
            ],
        ];
    }

}
