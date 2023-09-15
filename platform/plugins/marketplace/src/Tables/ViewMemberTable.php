<?php

namespace Botble\Marketplace\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Marketplace\Repositories\Eloquent\MemberPaketRepository;
use Botble\Table\Abstracts\TableAbstract;
use DateTime;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use RvMedia;
use Yajra\DataTables\DataTables;

class ViewMemberTable extends TableAbstract
{
    protected $hasActions = true;
    public $id = '';

    protected $hasFilter = false;

    protected $hasOperations = false;

    protected $hasCheckbox = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, MemberPaketRepository $memberPaketRepository)
    {
        $this->repository = $memberPaketRepository;
        parent::__construct($table, $urlGenerator);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->addColumn('name', function ($item) {
                return $item->customer->name;
            })
            ->addColumn('referral', function ($item) {
                return $item->code;
            })
            ->addColumn('join_date', function ($item) {
                return date_format(new DateTime($item->created_at), 'j M Y');
            })
            ->addColumn('expired_date', function ($item) {
                return date_format(new DateTime($item->expire_date), 'j M Y');
            })
            ->addColumn('email', function ($item) {
                return $item->customer->email;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = MemberPaket::where([
            ['id_paket', $this->id]
        ])
            ->whereNotNull('expire_date')
            ->with('customer');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'referral' => [
                'title' => 'Kode Referral',
                'class' => 'text-start',
            ],
            'email' => [
                'title' => 'Email',
                'class' => 'text-start',
            ],
            'join_date' => [
                'title' => 'Tanggal Join',
                'class' => 'text-start',
            ],
            'expired_date' => [
                'title' => 'Tanggal Expired',
                'class' => 'text-start',
            ],
        ];
    }
}
