<?php

namespace Botble\Accounting\Tables;

use Botble\Accounting\Repositories\Eloquent\CoaSaldoRepository;
use Botble\Table\Abstracts\TableAbstract;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CoaSaldoTable extends TableAbstract
{
    protected $hasActions = false;
    protected $hasCheckbox = false;
    protected int $pageLength = 30;
    protected $hasOperations = true;
    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CoaSaldoRepository $coaSaldoRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $coaSaldoRepository;
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('kredit',function($item){
                return format_price($item->kredit);
            })
            ->editColumn('debit',function($item){
                return format_price($item->debit);
            })
            ->addColumn('operations', function ($item) {
                $url = route('coa-saldo.edit', ['tahun' => $item->tahun, 'idcoa' => $item->idcoa]);
                $btnEdit = '<a href="'.$url.'" class="btn btn-icon btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-original-title="'.trans('core/base::tables.edit').'"><i class="fa fa-edit"></i></a>';
                return $this->getOperations('', '', $item,$btnEdit);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        // $query = $this->repository->getModel()->select('*');
        $query = $this->repository->getModel()->select([
            'idcoa',
            'namacoa',
            'kredit',
            'debit',
            'tahun'
        ])->orderBy('tahun','DESC');

        return $this->applyScopes($query);
    }

    public function getBulkChanges(): array
    {
        return [
            'tahun' => [
                'title' => 'Tahun',
                'type' => 'number',
                'validate' => 'required|max:4',
            ],
        ];
    }

    public function columns(): array
    {
        return [
            'tahun' => [
                'title' => 'ID Coa',
                'width' => '50px',
            ],
            'idcoa' => [
                'title' => 'ID Coa',
                'width' => '50px',
            ],
            'namacoa' => [
                'title' => 'Nama Coa',
                'class' => 'text-start',
            ],
            'kredit' => [
                'title' => 'Kredit',
                'class' => 'text-start',
            ],
            'debit' => [
                'title' => 'Debit',
                'class' => 'text-start',
            ],
        ];
    }
}
