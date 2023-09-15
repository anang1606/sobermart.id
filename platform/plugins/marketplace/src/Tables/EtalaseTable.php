<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Marketplace\Exports\ProductExport;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Table\Abstracts\TableAbstract;
use EcommerceHelper;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use MarketplaceHelper;
use RvMedia;
use Yajra\DataTables\DataTables;

class EtalaseTable extends TableAbstract
{
    protected $hasCheckbox = false;


    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ProductInterface $productRepository)
    {
        $this->repository = $productRepository;
        $this->hasOperations = true;
        $this->collapse = true;
        $this->colGroup = 1;
        $this->routeGroup = route('marketplace.vendor.etalase.details');
        $this->type = 'advanced';
        parent::__construct($table, $urlGenerator);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                return Html::link(route('marketplace.vendor.products.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('image', function ($item) {
                if ($this->request()->input('action') == 'csv') {
                    return RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());
                }

                if ($this->request()->input('action') == 'excel') {
                    return RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage());
                }

                return $this->displayThumbnail($item->image);
            })
            ->editColumn('etalase', function ($item) {
                $get_slug = \DB::select("SELECT * FROM slugs WHERE reference_id = '$item->store_id' AND prefix = 'stores' ")[0];
                $etalase = ($item->etalase === '' || $item->etalase === null) ? 'Semua Product' : BaseHelper::clean($item->etalase);
                return Html::link(str_replace('?','/',route('public.stores',$get_slug->key)).'/'.strtolower(str_replace(' ',
                '-', $item->etalase)),$etalase);
            })->editColumn('id', function ($item) {
                static $rowNumber = 0; // Variabel untuk menyimpan nomor baris
                $rowNumber++; // Tambahkan nomor baris setiap kali fungsi dipanggil
                return $rowNumber; // Kembalikan nomor baris
            })
            ->addColumn('operations', function ($item) {
                return view(MarketplaceHelper::viewPath('dashboard.table.actionsModal'), [
                    'edit' => 'marketplace.vendor.products.edit',
                    'delete' => 'marketplace.vendor.etalase.destroy',
                    'item' => $item,
                ])->render();
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select([
                'id',
                'name',
                'order',
                'created_at',
                'status',
                'sku',
                'images',
                'price',
                'sale_price',
                'sale_type',
                'start_date',
                'end_date',
                'etalase',
                'quantity',
                'with_storehouse_management',
                'product_type',
            ])
            ->where('is_variation', 0)
            ->where('store_id', auth('customer')->user()->store->id)
            ->groupBy('etalase')
            ->orderBy('etalase','ASC');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'etalase' => [
                'title' => 'Etalase',
                'class' => 'text-start',
            ],
        ];
    }

    public function getDefaultButtons(): array
    {
        return [
            'reload',
        ];
    }
}