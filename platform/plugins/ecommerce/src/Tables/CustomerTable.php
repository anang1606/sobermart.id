<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Table\Abstracts\TableAbstract;
use EcommerceHelper;
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

class CustomerTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CustomerInterface $customerRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $customerRepository;

        if (!Auth::user()->hasAnyPermission(['customers.edit', 'customers.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('avatar', function ($item) {
                if (
                    $this->request()->input('action') == 'excel' ||
                    $this->request()->input('action') == 'csv'
                ) {
                    return $item->avatar_url;
                }

                return Html::tag('img', '', ['src' => $item->avatar_url, 'alt' => BaseHelper::clean($item->name), 'width' => 50]);
            })
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('customers.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return BaseHelper::clean($item->name);
            })
            ->filterColumn('referral', function ($query, $keyword) {
                $getReferral = MemberPaket::whereRaw('LOWER(user_id) like ?', ["%{$keyword}%"])->first();
                if ($getReferral) {
                    return BaseHelper::clean($getReferral->code);
                }
            })
            ->addColumn('referral', function ($item) {
                $getReferral = MemberPaket::where('user_id', $item->id)->first();
                if ($getReferral) {
                    return Html::link(route('customers.view', $item->id), BaseHelper::clean($getReferral->code));
                }
            })
            ->addColumn('member', function ($item) {
                $pakets = $item->paket;
                $table = '';
                foreach ($pakets as $paket) {
                    if($paket->paket){
                        $total_member = Customer::join('ec_customer_pakets', 'ec_customers.id', '=', 'ec_customer_pakets.user_id')
                        ->where('ec_customer_pakets.parent', $item->id)
                        ->where('ec_customer_pakets.id_paket', $paket->id_paket)
                        ->count();

                        $table .= '<tr>';
                        $table .= '<td class="text-start">';
                        $table .= $paket->uuid;
                        $table .= '</td>';
                        $table .= '<td class="text-start">';
                        $table .= $paket->paket->name;
                        $table .= '</td>';
                        $table .= '<td class="text-end">';
                        $table .= $total_member;
                        $table .= '</td>';
                        $table .= '</tr>';
                    }
                }
                return "<table class=''><tbody>$table</tbody></table>";
            })
            ->editColumn('email', function ($item) {
                return BaseHelper::clean($item->email);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return BaseHelper::clean($item->status->toHtml());
            });

        if (EcommerceHelper::isEnableEmailVerification()) {
            $data = $data
                ->addColumn('confirmed_at', function ($item) {
                    return $item->confirmed_at ? Html::tag(
                        'span',
                        trans('core/base::base.yes'),
                        ['class' => 'text-success']
                    ) : trans('core/base::base.no');
                });
        }

        $data = $data
            ->addColumn('operations', function ($item) {
                $viewBtn = '';
                $viewBtn .= Html::link(
                    route('customers.view', $item->id),
                    '<i class="fa fa-eye"></i>',
                    [
                        'class' => 'btn btn-info',
                        'data-bs-toggle' => 'tooltip',
                        'data-bs-original-title' => 'Member',
                    ],
                    null,
                    false
                );
                $viewBtn .= Html::link(
                    route('customers.belanja', $item->id),
                    '<i class="fa fa-shopping-bag"></i>',
                    [
                        'class' => 'btn btn-warning',
                        'data-bs-toggle' => 'tooltip',
                        'data-bs-original-title' => 'Belanja',
                    ],
                    null,
                    false
                );

                return $this->getOperations('customers.edit', 'customers.destroy', $item, $viewBtn);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'name',
            'email',
            'avatar',
            'created_at',
            'status',
            'confirmed_at',
        ])
            ->with('paket.paket');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-start',
            ],
            'avatar' => [
                'title' => trans('plugins/ecommerce::customer.avatar'),
                'class' => 'text-center',
            ],
            'name' => [
                'title' => trans('core/base::forms.name'),
                'class' => 'text-start',
            ],
            'referral' => [
                'title' => 'Kode Referral',
                'class' => 'text-start',
            ],
            'member' => [
                'title' => 'Member',
                'class' => 'text-start',
            ],
            'email' => [
                'title' => trans('plugins/ecommerce::customer.email'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-start',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];

        if (EcommerceHelper::isEnableEmailVerification()) {
            $columns += [
                'confirmed_at' => [
                    'title' => trans('plugins/ecommerce::customer.email_verified'),
                    'width' => '100px',
                ],
            ];
        }

        return $columns;
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('customers.create'), 'customers.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('customers.deletes'), 'customers.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'email' => [
                'title' => trans('core/base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => CustomerStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', CustomerStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function renderTable($data = [], $mergeData = []): View|Factory|Response
    {
        if (
            $this->query()->count() === 0 &&
            $this->request()->input('filter_table_id') !== $this->getOption('id') && !$this->request()->ajax()
        ) {
            return view('plugins/ecommerce::customers.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
