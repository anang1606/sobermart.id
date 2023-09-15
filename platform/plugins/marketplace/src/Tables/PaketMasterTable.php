<?php

namespace Botble\Marketplace\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Marketplace\Repositories\Eloquent\PaketMasterRepository;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use RvMedia;
use Yajra\DataTables\DataTables;

class PaketMasterTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = false;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PaketMasterRepository $storeRepository)
    {
        $this->repository = $storeRepository;
        parent::__construct($table, $urlGenerator);

        if (! Auth::user()->hasAnyPermission(['marketplace.store.edit', 'marketplace.store.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('image', function ($item) {
                return Html::image(
                    RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage()),
                    BaseHelper::clean($item->name),
                    ['width' => 50]
                );
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->addColumn('operations', function ($item) {
                $viewBtn = '';
                $viewBtn .= Html::link(
                    route('marketplace.paket_master.view', $item->id),
                    '<i class="fa fa-eye"></i>',
                    [
                        'class' => 'btn btn-info',
                        'data-bs-toggle' => 'tooltip',
                        'data-bs-original-title' => 'Member',
                    ],
                    null,
                    false
                );
                return $this->getOperations('marketplace.paket_master.edit', 'marketplace.paket_master.destroy', $item, $viewBtn);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select('*');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image' => [
                'title' => trans('core/base::tables.image'),
                'width' => '70px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('marketplace.paket_master.create'), 'marketplace.paket_master.create');
    }
}
