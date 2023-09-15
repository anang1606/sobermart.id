<?php

namespace Botble\Accounting\Tables;

use Botble\Accounting\Repositories\Eloquent\CoaRepository;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CoaTable extends TableAbstract
{
    protected $hasActions = false;
    protected $hasCheckbox = false;
    protected int $pageLength = 30;
    protected $hasOperations = false;
    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CoaRepository $coaRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $coaRepository;

        if (! Auth::user()->hasAnyPermission(['payment.show', 'payment.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query());

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
            'idcoa' => [
                'title' => 'ID Coa',
                'width' => '50px',
            ],
            'namacoa' => [
                'title' => 'Nama Coa',
                'class' => 'text-start',
            ],
            'typecoa' => [
                'title' => 'Type Coa',
                'class' => 'text-start',
            ],
        ];
    }

}
