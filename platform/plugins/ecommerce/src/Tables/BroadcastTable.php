<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Payment\Repositories\Eloquent\BroadcastRepository;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class BroadcastTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, BroadcastRepository $broadcastRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $broadcastRepository;

        if (! Auth::user()->hasAnyPermission(['ecommerce.broadcast.edit', 'ecommerce.broadcast.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('title', function ($item) {
                return BaseHelper::clean($item->title);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('image', function ($item) {
                return $this->displayThumbnail($item->image);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('', 'ecommerce.broadcast.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
        ->select('*')
        ->whereNotIn('title', [
            'Pengingat Masa Aktif Paket',
            'Pengingat Target Member',
            'Masa Aktif Paket'
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-start',
            ],
            'image' => [
                'title' => trans('plugins/ecommerce::brands.logo'),
                'class' => 'text-start',
                'width' => '50px',
            ],
            'title' => [
                'title' => trans('core/base::tables.title'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-start',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('ecommerce.broadcast.create'), 'ecommerce.broadcast.create');
    }

}
