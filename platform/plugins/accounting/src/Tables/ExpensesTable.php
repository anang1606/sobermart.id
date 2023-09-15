<?php

namespace Botble\Accounting\Tables;

use App\Models\User;
use Botble\Accounting\Models\Coa;
use Botble\Accounting\Repositories\Eloquent\ExpensesRepository;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ExpensesTable extends TableAbstract
{
    protected $hasActions = true;
    protected $hasCheckbox = false;
    protected int $pageLength = 30;
    protected $hasOperations = false;
    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ExpensesRepository $expenseRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $expenseRepository;
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('coadebit',function($item){
                $coadebit = Coa::where('idcoa',$item->coadebit)->first();
                return $coadebit->namacoa;
            })
            ->editColumn('coakredit',function($item){
                $coakredit = Coa::where('idcoa',$item->coakredit)->first();
                return $coakredit->namacoa;
            })
            ->editColumn('user_id',function($item){
                $user = User::where('id',$item->user_id)->first();
                return $user->first_name . ' ' .$user->last_name;
            })
            ->editColumn('note',function($item){
                return ($item->note === null) ? 'Tidak ada Keterangan' : $item->note;
            })
            ->editColumn('amount',function($item){
                return format_price($item->amount);
            });
            // ->addColumn('operations', function ($item) {
            //     return $this->getOperations('ads.edit', 'ads.destroy', $item);
            // });

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
            'date' => [
                'title' => 'Tanggal',
                'class' => 'text-start',
            ],
            'kode_reff' => [
                'title' => 'No. Refferensi',
                'class' => 'text-start',
            ],
            'note' => [
                'title' => 'Catatan',
                'class' => 'text-start',
            ],
            'coadebit' => [
                'title' => 'Akun Debit',
                'class' => 'text-start',
            ],
            'coakredit' => [
                'title' => 'Akun Kredit',
                'class' => 'text-start',
            ],
            'user_id' => [
                'title' => 'User',
                'class' => 'text-start',
            ],
            'amount' => [
                'title' => 'Total',
                'class' => 'text-start',
            ],
        ];
    }

    public function buttons(): array
    {
        $buttons = [];
        $buttons = $this->addCreateButton(route('expense.create'), 'expense.create');

        return $buttons;
    }

}
