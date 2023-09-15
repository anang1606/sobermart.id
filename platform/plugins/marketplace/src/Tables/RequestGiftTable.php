<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Marketplace\Repositories\Eloquent\RequestGiftRepository;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class RequestGiftTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;
    protected $hasCheckbox = false;
    protected $rowNumber = 1;

    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        RequestGiftRepository $requestGiftRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $requestGiftRepository;
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->addColumn('hadiah', function ($item) {
                foreach ($item->paket->details as $details) {
                    if($details->target === $item->target){
                        $item->details = $details;
                    }
                }
                unset($item->paket->details);
                return (isset($item->details->label)) ? $item->details->label : '' ;
            })
            ->editColumn('paket_id', function ($item) {
                $nominal = format_price($item->paket->nominal);
                return $item->paket->name . " ($nominal)";
            })
            ->editColumn('customer', function ($item) {
                return $item->customer->name;
            })
            ->editColumn('status', function ($item) {
                return $item->status;
            })
            ->addColumn('no', function ($item) use(&$rowNumber) {
                $rowNumber++;
                return $rowNumber;
            })
            ->addColumn('operations', function ($item) {
                $viewBtn = '';
                return $this->getOperations('marketplace.request-gift.edit', '', $item, $viewBtn);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select('*')
            ->with(
                'customer',
                'paket.details'
            );

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'no' => [
                'title' => 'No.',
                'width' => '20px',
            ],
            'customer' => [
                'title' => 'Nama Customer',
                'class' => 'text-start',
                'width' => '100px',
            ],
            'nama' => [
                'title' => 'Nama KTP',
                'class' => 'text-start',
                'width' => '100px',
            ],
            'paket_id' => [
                'title' => 'Paket',
                'class' => 'text-start',
            ],
            'hadiah' => [
                'title' => 'Hadiah',
                'class' => 'text-start',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => WithdrawalStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(WithdrawalStatusEnum::values()),
            ],
        ];
    }
}
