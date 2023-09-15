<?php

namespace Botble\PopupAds\Tables;

use Auth;
use BaseHelper;
use Botble\PopupAds\Repositories\Eloquent\PopupAdsRepository;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use RvMedia;
use Yajra\DataTables\DataTables;

class PopupAdsTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    protected $rowNumber = 0;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PopupAdsRepository $adsCategoryRepository)
    {
        $this->repository = $adsCategoryRepository;
        parent::__construct($table, $urlGenerator);

        if (! Auth::user()->hasAnyPermission(['ads.edit', 'ads.destroy'])) {
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
                    $item->name,
                    ['width' => 50]
                );
            })
            ->editColumn('name', function ($item) {
                if (! Auth::user()->hasPermission('popup-ads.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('popup-ads.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->addColumn('No',function($item) use (&$rowNumber){
                $rowNumber++;
                return $rowNumber;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('expired_at', function ($item) {
                return BaseHelper::formatDate($item->expired_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        if (function_exists('shortcode')) {
            $data = $data->editColumn('key', function ($item) {
                return generate_shortcode('ads-category', ['key' => $item->key]);
            });
        }

        $data = $data->addColumn('operations', function ($item) {
            return $this->getOperations('popup-ads.edit', 'popup-ads.destroy', $item);
        });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'image',
            'key',
            'name',
            'clicked',
            'expired_at',
            'status',
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'No' => [
                'title' => 'No.',
                'width' => '20px',
            ],
            'image' => [
                'name' => 'image',
                'title' => trans('core/base::tables.image'),
                'width' => '70px',
            ],
            'name' => [
                'name' => 'name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'clicked' => [
                'name' => 'clicked',
                'title' => trans('plugins/ads::ads.clicked'),
                'class' => 'text-start',
            ],
            'expired_at' => [
                'name' => 'expired_at',
                'title' => trans('plugins/ads::ads.expired_at'),
                'width' => '100px',
            ],
            'status' => [
                'name' => 'status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('popup-ads.create'), 'popup-ads.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Ads::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('popup-ads.deletes'), 'popup-ads.destroy', parent::bulkActions());
    }

    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }

    public function getBulkChanges(): array
    {
        return [
            'ads.name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'ads.status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ads.expired_at' => [
                'title' => trans('plugins/ads::ads.expired_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
