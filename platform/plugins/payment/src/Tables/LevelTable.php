<?php

namespace Botble\Payment\Tables;

use BaseHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Eloquent\LevelRepository;
use Botble\Payment\Repositories\Interfaces\BankListInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use RvMedia;

class LevelTable extends TableAbstract
{
    protected $hasActions = false;

    protected $hasCheckbox = false;

    protected $hasOperations = false;

    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, LevelRepository $levelRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $levelRepository;

        if (! Auth::user()->hasAnyPermission(['payment.show', 'payment.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('total_member_pack', function ($item) {
                return format_price($item->total_member_pack);
            })
            ->editColumn('belanja_pribadi', function ($item) {
                return format_price($item->belanja_pribadi);
            })
            ->editColumn('profit_assumption', function ($item) {
                return format_price($item->profit_assumption);
            })
            ->editColumn('assumsition_perusahaan', function ($item) {
                return format_price($item->assumsition_perusahaan);
            })
            ->editColumn('pendapatan_dari_adm', function ($item) {
                return format_price($item->pendapatan_dari_adm);
            })
            ->editColumn('accumulation_administrative_income', function ($item) {
                return format_price($item->accumulation_administrative_income);
            })
            ->editColumn('net_assumsition', function ($item) {
                return format_price($item->net_assumsition);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select('*');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'total_member' => [
                'title' => 'Total Member',
                'width' => '20px',
            ],
            'total_member_pack' => [
                'title' => 'Total Member Pack',
                'width' => '20px',
            ],
            // 'total_member_level' => [
            //     'title' => 'Total Member Level',
            //     'width' => '20px',
            // ],
            // 'belanja_per_bulan' => [
            //     'title' => 'Belanja per Bulan',
            //     'width' => '20px',
            // ],
            'belanja_pribadi' => [
                'title' => 'Belanja Pribadi',
                'width' => '20px',
            ],
            // 'total_group' => [
            //     'title' => 'Total Group',
            //     'width' => '20px',
            // ],
            // 'total_pendapatan_per_bulan' => [
            //     'title' => 'Total Pendapatan per Bulan',
            //     'width' => '20px',
            // ],
            'profit_assumption' => [
                'title' => 'Proffit Assumption',
                'width' => '20px',
            ],
            'assumsition_perusahaan' => [
                'title' => 'Assumsition Perusahaan',
                'width' => '20px',
            ],
            'pendapatan_dari_adm' => [
                'title' => 'Pendapatan dari Adm',
                'width' => '20px',
            ],
            'accumulation_administrative_income' => [
                'title' => 'Accumulation Administrative Income',
                'width' => '20px',
            ],
            'net_assumsition' => [
                'title' => 'Net Assumsition',
                'width' => '20px',
            ],
        ];
    }

}
