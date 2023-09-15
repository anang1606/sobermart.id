<?php

namespace Botble\Payment\Tables;

use BaseHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Eloquent\BankListRepository;
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

class BankTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, BankListRepository $bankListRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $bankListRepository;

        if (! Auth::user()->hasAnyPermission(['payment.show', 'payment.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('icons', function ($item) {
                return '<img width="20%" src="'.RvMedia::url($item->icons).'" />';
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('bank.show', 'bank.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'bank_code',
            'bank_holder',
            'bank_nomor',
            'bank_name',
            'icons',
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => 'id',
                'width' => '20px',
            ],
            'icons' => [
                'title' => 'Icons',
                'class' => '50px',
            ],
            'bank_name' => [
                'title' => 'Bank Name',
                'class' => 'text-start',
            ],
            'bank_holder' => [
                'title' => 'Bank Holder',
                'class' => 'text-start',
            ],
            'bank_nomor' => [
                'title' => 'Nomor Rekening',
                'class' => 'text-start',
            ],
        ];
    }

    public function buttons(): array
    {
        $buttons = [];
        $buttons = $this->addCreateButton(route('bank.create'), 'bank.create');

        return $buttons;
    }
}