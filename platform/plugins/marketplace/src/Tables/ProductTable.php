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

class ProductTable extends TableAbstract
{
    protected $hasCheckbox = false;
    protected $rowNumber = 0;

    protected string $exportClass = ProductExport::class;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ProductInterface $productRepository)
    {
        $this->repository = $productRepository;
        $this->tableLang = true;
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
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('price', function ($item) {
                return $item->price_in_table;
            })
            ->editColumn('quantity', function ($item) {
                return $item->with_storehouse_management ? $item->quantity : '&#8734;';
            })
            ->editColumn('sku', function ($item) {
                return BaseHelper::clean($item->sku ?: '&mdash;');
            })
            ->editColumn('order', function ($item) {
                return (string) $item->order;
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->addColumn('No', function ($item) use (&$rowNumber) {
                $rowNumber++; // Tambahkan nomor baris setiap kali fungsi dipanggil
                return $rowNumber; // Kembalikan nomor baris
            })
            ->addColumn('operations', function ($item) {
                return view(MarketplaceHelper::viewPath('dashboard.table.actions'), [
                    'edit' => 'marketplace.vendor.products.edit',
                    'delete' => 'marketplace.vendor.products.destroy',
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
                'quantity',
                'with_storehouse_management',
                'product_type',
            ])
            ->where('is_variation', 0)
            ->where('store_id', auth('customer')->user()->store->id);

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
                'name' => 'images',
                'title' => trans('plugins/ecommerce::products.image'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'sale_price' => [
                'title' => trans('plugins/ecommerce::products.price'),
                'class' => 'text-start',
            ],
            'quantity' => [
                'title' => trans('plugins/ecommerce::products.quantity'),
                'class' => 'text-start',
            ],
            'sku' => [
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-start',
            ],
            'order' => [
                'title' => trans('core/base::tables.order'),
                'width' => '50px',
                'class' => 'text-center',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-center',
            ],
        ];
    }

    public function buttons(): array
    {
        if (EcommerceHelper::isEnabledSupportDigitalProducts()) {
            $buttons['create'] = [
                'extend' => 'collection',
                'text' => view('core/table::partials.create')->render(),
                'buttons' => [
                    [
                        'className' => 'action-item',
                        'text' => ProductTypeEnum::PHYSICAL()->toIcon() . ' ' . Html::tag('span', ProductTypeEnum::PHYSICAL()->label(), [
                            'data-action' => 'physical-product',
                            'data-href' => route('marketplace.vendor.products.create'),
                            'class' => 'ms-1',
                        ])->toHtml(),
                    ],
                    [
                        'className' => 'action-item',
                        'text' => ProductTypeEnum::DIGITAL()->toIcon() . ' ' . Html::tag('span', ProductTypeEnum::DIGITAL()->label(), [
                            'data-action' => 'digital-product',
                            'data-href' => route('marketplace.vendor.products.create', ['product_type' => 'digital']),
                            'class' => 'ms-1',
                        ])->toHtml(),
                    ],
                ],
            ];
        } else {
            $buttons = $this->addCreateButton(route('marketplace.vendor.products.create'));
        }

        return $buttons;
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('marketplace.vendor.products.deletes'), null, parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'order' => [
                'title' => trans('core/base::tables.order'),
                'type' => 'number',
                'validate' => 'required|min:0',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
