<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Forms\CustomerForm;
use Botble\Ecommerce\Http\Requests\AddCustomerWhenCreateOrderRequest;
use Botble\Ecommerce\Http\Requests\CustomerCreateRequest;
use Botble\Ecommerce\Http\Requests\CustomerEditRequest;
use Botble\Ecommerce\Http\Requests\CustomerUpdateEmailRequest;
use Botble\Ecommerce\Http\Resources\CustomerAddressResource;
use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\BroadcastRead;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Tables\CustomerTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Payment\Enums\PaymentStatusEnum;
use DB;
use Botble\Ecommerce\Models\MemberWithdrawal;
class CustomerController extends BaseController
{
    protected CustomerInterface $customerRepository;

    protected AddressInterface $addressRepository;

    public function __construct(CustomerInterface $customerRepository, AddressInterface $addressRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
    }

    public function index(CustomerTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::customer.name'));

        return $dataTable->renderTable();
    }
    public function viewMember(int $id)
    {
        $customer = $this->customerRepository->findOrFail($id);

        if (!$customer->id) {
            abort(404);
        }

        $members = [];
        $member_list = [];
        $parent_paket = MemberPaket::where('user_id', $id)->get();

        $customer_member = MemberPaket::where('parent', $id)->get();
        $parentPaket = [];
        foreach ($parent_paket as $pk) {
            $parentPaket[] = $pk->id_paket;
        }
        foreach ($customer_member as $cm) {
            $member_list[] = $cm->user_id;
        }
        $paket_customer = MemberPaket::whereIn('user_id', $member_list)
            ->whereIn('id_paket', $parentPaket)
            ->whereNotNull('expire_date')
            ->with('paket', 'customer')
            ->get();

        $members = $paket_customer->groupBy('id_paket')->map(function ($items) {
            $firstItem = $items->first();
            return [
                // 'id_paket' => $firstItem->paket->id,
                'paket' => $firstItem->paket->name,
                'nominal' => $firstItem->paket->nominal,
                'members' => $items->map(function ($item) use ($firstItem) {
                    $joinDate = Carbon::parse($item->created_at);
                    $expiredDate = Carbon::parse($item->expire_date);
                    $carbonNow = Carbon::now();
                    $currentMonth = Carbon::now()->month;
                    $currentYear = Carbon::now()->year;
                    $startDate = Carbon::now();

                    if ($joinDate->month === $currentMonth && $joinDate->year === $currentYear) {
                        $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $expiredDate->day);
                        $nextDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day)->addMonth();
                    } else {
                        // $endDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day);
                        if ($expiredDate->isBefore($startDate)) {
                            $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $expiredDate->day);
                            $nextDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day)->addMonth();
                        } else {
                            $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $expiredDate->day)->subMonth();
                            $nextDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day);
                        }
                    }

                    // return $nextDate;

                    $total_belanja = Order::where([
                        ['user_id', $item->customer->id],
                        ['id_paket', $item->id],
                        ['status', '<>', OrderStatusEnum::CANCELED()],
                        ['status', '<>', OrderStatusEnum::RETURNED()]
                    ])
                        ->whereHas('payment', function ($query) {
                            $query->where('status', PaymentStatusEnum::COMPLETED());
                        })
                        ->whereDate('created_at', '<=', $nextDate)
                        ->whereDate('created_at', '>=', $previousDate)
                        ->selectRaw('SUM(amount - shipping_amount) as total_amount')
                        ->first()
                        ->total_amount;

                    return [
                        'id' => $item->customer->id,
                        'name' => $item->customer->name,
                        'paket' => $item,
                        'total_belanja' => (int)$total_belanja,
                        'status' => ((int)$total_belanja >= $item->paket->nominal) ? 'Completed' : 'Pending',
                        'commission' => ($firstItem->paket->fee_commissions / 100) * (int)$total_belanja,
                        'join_date' => $joinDate,
                        'expired_date' => $expiredDate,
                        // 'commission' => ((int)$total_belanja > $item->paket->nominal) ? (4/100) * (int)$total_belanja : 0,
                    ];
                }),
            ];
        })->values();

        $total_witdrawal = MemberWithdrawal::where('customer_id', $id)->sum('amount');
        $get_wihdrawal = MemberWithdrawal::where('customer_id', $id)->get();

        $is_member = MemberPaket::where('user_id', $id)->first();

        // return $customer;
        return view('plugins/marketplace::customers.member', compact('members', 'get_wihdrawal', 'is_member', 'total_witdrawal','customer'))->render();
    }

    public function viewBelanja(int $id)
    {
        $customer = $this->customerRepository->findOrFail($id);

        $orders = Order::where([['user_id', $customer->id], ['status', 'completed']])->with('products', 'payment')->get();
        // return $orders;
        return view('plugins/marketplace::customers.belanja', compact('orders'))->render();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ecommerce::customer.create'));

        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/customer.js');

        return $formBuilder->create(CustomerForm::class)->remove('is_change_password')->renderForm();
    }

    public function store(CustomerCreateRequest $request, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->getModel();
        $customer->fill($request->input());
        $customer->confirmed_at = Carbon::now();
        $customer->password = Hash::make($request->input('password'));
        $customer->dob = Carbon::parse($request->input('dob'))->toDateString();
        $customer = $this->customerRepository->createOrUpdate($customer);

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customers.index'))
            ->setNextUrl(route('customers.edit', $customer->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int $id, FormBuilder $formBuilder)
    {
        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/customer.js');

        $customer = $this->customerRepository->findOrFail($id);

        page_title()->setTitle(trans('plugins/ecommerce::customer.edit', ['name' => $customer->name]));

        $customer->password = null;

        return $formBuilder->create(CustomerForm::class, ['model' => $customer])->renderForm();
    }

    public function update(int $id, CustomerEditRequest $request, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findOrFail($id);

        $customer->fill($request->except('password'));

        if ($request->input('is_change_password') == 1) {
            $customer->password = Hash::make($request->input('password'));
        }

        $customer->dob = Carbon::parse($request->input('dob'))->toDateString();

        $customer = $this->customerRepository->createOrUpdate($customer);

        event(new UpdatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customers.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $customer = $this->customerRepository->findOrFail($id);
            $this->customerRepository->delete($customer);
            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $customer = $this->customerRepository->findOrFail($id);
            $this->customerRepository->delete($customer);
            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function verifyEmail(int $id, Request $request, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->getFirstBy([
            'id' => $id,
            'confirmed_at' => null,
        ]);

        if (!$customer) {
            abort(404);
        }

        $customer->confirmed_at = Carbon::now();
        $customer->save();

        event(new UpdatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customers.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getListCustomerForSelect(BaseHttpResponse $response)
    {
        $customers = $this->customerRepository
            ->allBy([], [], ['id', 'name'])
            ->toArray();

        return $response->setData($customers);
    }

    public function getListCustomerForSearch(Request $request, BaseHttpResponse $response)
    {
        $customers = $this->customerRepository
            ->getModel()
            ->where('name', 'LIKE', '%' . $request->input('keyword') . '%')
            ->simplePaginate(5);

        foreach ($customers as &$customer) {
            $customer->avatar_url = (string)$customer->avatar_url;
        }

        return $response->setData($customers);
    }

    public function postUpdateEmail($id, CustomerUpdateEmailRequest $request, BaseHttpResponse $response)
    {
        $this->customerRepository->createOrUpdate(['email' => $request->input('email')], ['id' => $id]);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getCustomerAddresses($id, BaseHttpResponse $response)
    {
        $addresses = $this->addressRepository->allBy(['customer_id' => $id]);

        return $response->setData(CustomerAddressResource::collection($addresses));
    }

    public function getCustomerOrderNumbers($id, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            return $response->setData(0);
        }

        return $response->setData($customer->orders()->count());
    }

    public function postCreateCustomerWhenCreatingOrder(
        AddCustomerWhenCreateOrderRequest $request,
        BaseHttpResponse $response
    ) {
        $request->merge(['password' => Hash::make(Str::random(36))]);
        $customer = $this->customerRepository->createOrUpdate($request->input());
        $customer->avatar = (string)$customer->avatar_url;

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        $request->merge([
            'customer_id' => $customer->id,
            'is_default' => true,
        ]);

        $address = $this->addressRepository->createOrUpdate($request->input());

        $address->country = $address->country_name;
        $address->state = $address->state_name;
        $address->city = $address->city_name;

        $address->country_name = $address->country;
        $address->state_name = $address->state;
        $address->city_name = $address->city;

        return $response
            ->setData(compact('address', 'customer'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }
}
