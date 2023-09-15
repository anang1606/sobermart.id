<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Arr;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\AvatarRequest;
use Botble\Ecommerce\Http\Requests\EditAccountRequest;
use Botble\Ecommerce\Http\Requests\OrderReturnRequest;
use Botble\Ecommerce\Http\Requests\UpdatePasswordRequest;
use Botble\Ecommerce\Models\AhliWaris;
use Botble\Ecommerce\Models\SupportMessage;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderReturnInterface;
use Botble\Ecommerce\Events\OrderCompletedEvent;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Media\Services\ThumbnailService;
use Botble\Media\Supports\Zipper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use EcommerceHelper;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvoiceHelper;
use OrderHelper;
use OrderReturnHelper;
use RvMedia;
use SeoHelper;
use Theme;
use Botble\Payment\Models\Payment;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\PaketMaster;
use Botble\Ecommerce\Models\BankList;
use Botble\Ecommerce\Models\MemberWithdrawal;
use Botble\Ecommerce\Models\PaymentFile;
use ItemsHelper;
use Illuminate\Support\Facades\App;

use Botble\Marketplace\Repositories\Interfaces\RevenueInterface;
use Botble\Marketplace\Repositories\Interfaces\VendorInfoInterface;
use Botble\Marketplace\Models\CategoryCommission;
use MarketplaceHelper;
use Illuminate\Support\Facades\DB;
use Throwable;
use Botble\Marketplace\Http\Requests\MemberWithdrawalRequest;
use Botble\Marketplace\Enums\WithdrawalStatusEnum;
use Botble\Base\Forms\FormBuilder;
use Botble\Ecommerce\Enums\OrderReturnStatusEnum;
use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\BroadcastCustomer;
use Botble\Ecommerce\Models\BroadcastRead;
use Botble\Ecommerce\Models\CommisionsEstimasi;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Marketplace\Forms\ShowMemberWithdrawalForm;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicController extends Controller
{
    protected CustomerInterface $customerRepository;

    protected ProductInterface $productRepository;

    protected AddressInterface $addressRepository;

    protected OrderInterface $orderRepository;

    protected OrderHistoryInterface $orderHistoryRepository;

    protected OrderReturnInterface $orderReturnRepository;

    protected OrderProductInterface $orderProductRepository;

    protected ReviewInterface $reviewRepository;

    public function __construct(
        CustomerInterface $customerRepository,
        ProductInterface $productRepository,
        AddressInterface $addressRepository,
        OrderInterface $orderRepository,
        OrderHistoryInterface $orderHistoryRepository,
        OrderReturnInterface $orderReturnRepository,
        OrderProductInterface $orderProductRepository,
        ReviewInterface $reviewRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->orderReturnRepository = $orderReturnRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->reviewRepository = $reviewRepository;

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css');

        Theme::asset()
            ->container('footer')
            ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'])
            ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'])
            ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery']);

        if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation()) {
            Theme::asset()
                ->container('footer')
                ->add('location-js', 'vendor/core/plugins/location/js/location.js', ['jquery']);
        }
    }

    public function getOverview()
    {
        SeoHelper::setTitle(__('Account information'));

        $currentUser = auth('customer')->user();
        $member_pakets = MemberPaket::with(['paket' => function ($query) {
            $query->where('deleted_at', null);
        }])
            ->where([['user_id', $currentUser->id], ['expire_date', '!=', Null]])
            ->whereHas('paket')
            ->get();

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Account information'), route('customer.overview'));

        return Theme::scope(
            'ecommerce.customers.overview',
            compact('member_pakets'),
            'plugins/ecommerce::themes.customers.overview'
        )->render();
    }

    public function getEditAccount()
    {
        SeoHelper::setTitle(__('Profile'));

        Theme::asset()
            ->add(
                'datepicker-style',
                'vendor/core/core/base/libraries/bootstrap-datepicker/css/bootstrap-datepicker3.min.css',
                ['bootstrap']
            );
        Theme::asset()
            ->container('footer')
            ->add(
                'datepicker-js',
                'vendor/core/core/base/libraries/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
                ['jquery']
            );

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Profile'), route('customer.edit-account'));

        return Theme::scope('ecommerce.customers.edit-account', [], 'plugins/ecommerce::themes.customers.edit-account')
            ->render();
    }

    public function postEditAccount(EditAccountRequest $request, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->createOrUpdate(
            $request->except('email'),
            [
                'id' => auth('customer')->id(),
            ]
        );

        do_action(HANDLE_CUSTOMER_UPDATED_ECOMMERCE, $customer, $request);

        return $response
            ->setNextUrl(route('customer.edit-account'))
            ->setMessage(__('Update profile successfully!'));
    }

    public function getChangePassword()
    {
        SeoHelper::setTitle(__('Change Password'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Change Password'), route('customer.change-password'));

        return Theme::scope(
            'ecommerce.customers.change-password',
            [],
            'plugins/ecommerce::themes.customers.change-password'
        )->render();
    }

    public function postChangePassword(UpdatePasswordRequest $request, BaseHttpResponse $response)
    {
        $currentUser = auth('customer')->user();

        if (!Hash::check($request->input('old_password'), $currentUser->getAuthPassword())) {
            return $response
                ->setError()
                ->setMessage(trans('acl::users.current_password_not_valid'));
        }

        $this->customerRepository->update(['id' => auth('customer')->id()], [
            'password' => Hash::make($request->input('password')),
        ]);

        return $response->setMessage(trans('acl::users.password_update_success'));
    }

    public function getListPayments(Request $request)
    {
        SeoHelper::setTitle(__('Waiting Payments'));
        $currentUser = auth('customer')->user();
        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Waiting Payments'), route('customer.payments'));

        $payments = Payment::selectRaw('*, SUM(amount) as amount')
            ->where([
                ['customer_id', $currentUser->id],
                ['status', 'pending'],
            ])
            ->orWhere([
                ['customer_id', $currentUser->id],
                ['status', 'failed'],
            ])
            ->groupBy('charge_id')
            ->orderBy('updated_at', 'DESC')
            ->get();

        // return $payments;
        foreach ($payments as $payemnt) {
            date_default_timezone_set('Asia/Jakarta');
            if (strtotime($payemnt->expiry_time) < time()) {
                $GET_ALL_PAYMENTS = Payment::where('order_id', $payemnt->order_id)->get();
                foreach ($GET_ALL_PAYMENTS as $GAP) {
                    $ORDER_ID = Order::where('id', $GAP->order_id)->first();
                    if ($ORDER_ID) {
                        OrderHelper::cancelOrder($ORDER_ID);
                    }
                    // $ORDER_ID->status = 'canceled';
                    // $ORDER_ID->save();
                    // $GAP->status = 'failed';
                    // $GAP->save();
                }
            }
            if ($payemnt->payment_channel->label() === 'Bank transfer') {
                $get_bank_list = BankList::where([['bank_code', $payemnt->bank], ['id', $payemnt->va_number]])->first();

                if (!$get_bank_list) {
                    $get_bank_list = BankList::first();
                }
                $payemnt->va_number = $get_bank_list->bank_nomor;
                $payemnt->bank_holder = $get_bank_list->bank_holder;
            } else {
                $payemnt->bank_holder = '';
            }
        }

        return Theme::scope(
            'ecommerce.customers.payments.list',
            compact('payments'),
            'plugins/ecommerce::themes.customers.payments.list'
        )->render();
    }

    public function getListHistory(Request $request)
    {
        SeoHelper::setTitle(__('Orders'));

        $orders = $this->orderRepository->advancedGet([
            'condition' => [
                'user_id' => auth('customer')->id(),
                'status' => OrderStatusEnum::COMPLETED()
            ],
            'paginate' => [
                'per_page' => 10,
                'current_paged' => (int)$request->input('page'),
            ],
            'withCount' => ['products'],
            'order_by' => ['created_at' => 'DESC'],
        ]);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Orders'), route('customer.orders'));

        // return $orders;
        return Theme::scope(
            'ecommerce.customers.orders.list',
            compact('orders'),
            'plugins/ecommerce::themes.customers.orders.list'
        )->render();
    }
    public function getListOrders(Request $request)
    {
        SeoHelper::setTitle(__('Orders'));

        $orders = Order::where('user_id', auth('customer')->id())
            ->whereIn('status', [OrderStatusEnum::PENDING(), OrderStatusEnum::PROCESSING(), OrderStatusEnum::CANCELED()])
            ->orderBy('created_at', 'DESC')
            ->withCount('products')
            ->paginate(10);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Orders'), route('customer.orders'));

        // return $orders;
        return Theme::scope(
            'ecommerce.customers.orders.list',
            compact('orders'),
            'plugins/ecommerce::themes.customers.orders.list'
        )->render();
    }

    public function notifications(Request $request)
    {
        SeoHelper::setTitle(__('Notifications'));

        $customerNotifications = BroadcastCustomer::where('customer_id', auth('customer')->id())
            ->with('broadcast')
            ->latest()
            ->get();

        $notif_customer = [];
        foreach ($customerNotifications as $get_notification) {
            $notif_customer[] = $get_notification->broadcast;
        }

        $allNotifications = Broadcast::where('target', 'all')
            ->latest()
            ->get();

        $notifications = collect($notif_customer)->concat($allNotifications)
            ->sortByDesc('created_at');

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;

        $paginatedNotifications = new LengthAwarePaginator(
            $notifications->forPage($currentPage, $perPage),
            $notifications->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        foreach ($paginatedNotifications as $notification) {
            $is_read = BroadcastRead::where([
                ['customer_id', auth('customer')->id()],
                ['broadcast_id', $notification->id],
            ])->first();

            $notification->is_read = $is_read ? 1 : 0;

            $template = $notification->description;
            $username = auth('customer')->user()->name;
            $product = Product::find($notification->product_id);
            $limit = 20;

            $template = preg_replace('/{{\s*customer_name\s*}}/', $username, $template);

            if ($product) {
                $productLimited = '"' . substr($product->name, 0, $limit) . '..."';
                $template = preg_replace('/{{\s*product_name\s*}}/', $productLimited, $template);
            }

            $template = preg_replace('/{{\s*[^{}]+\s*}}/', '', $template);

            $notification->description = $template;
        }

        // return $paginatedNotifications;
        return Theme::scope(
            'ecommerce.customers.notification',
            compact('paginatedNotifications'),
            'plugins/ecommerce::themes.customers.notification'
        )->render();
    }

    private function getTrackingJNE($awb)
    {
        $curl = curl_init();
        $url = 'http://apiv2.jne.co.id:10102/tracing/api/list/v1/cnote/' . $awb;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username=TESTAPI&api_key=25c898a9faea1a100859ecd9ef674548',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    private function getTrackingJNT($awb)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://secure-jk.jet.co.id/jandt-order-web/track/trackAction!tracking.action',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '{
            "awb": "' . $awb . '",
            "eccompanyid": "SOBERMART"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain',
                'Authorization: Basic U09CRVJNQVJUOk9mbVphZ0xiZUQ1Qg=='
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function getViewOrder(int $id)
    {
        $order = $this->orderRepository->getFirstBy(
            [
                'id' => $id,
                'user_id' => auth('customer')->id(),
            ],
            ['ec_orders.*'],
            ['address', 'products']
        );

        if (!$order) {
            abort(404);
        }

        SeoHelper::setTitle(__('Order detail :id', ['id' => $order->code]));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(
                __('Order detail :id', ['id' => $order->code]),
                route('customer.orders.view', $id)
            );

        $shipment = Shipment::where('order_id', $order->id)->first();

        if ($shipment->shipment_id !== '') {
            if (strpos($shipment->shipping_company_name, 'J&T') !== false) {

                $shipment->tracking = $this->getTrackingJNT($shipment->shipment_id);
            } else {
                $awb = $shipment->shipment_id;
                $shipment->tracking =  $this->getTrackingJNE($awb);
                // ($this->getTracking($awb) && !$this->getTracking($awb)->status) ? $shipment->tracking =  $this->getTracking($awb) : null;
            }
        }
        $order->shipment = $shipment;

        $order_history = OrderHistory::where('order_id', $order->id)->orderBy('created_at', 'DESC')->get();
        $order->order_histories = $order_history;
        // return $order;
        return Theme::scope(
            'ecommerce.customers.orders.view',
            compact('order'),
            'plugins/ecommerce::themes.customers.orders.view'
        )->render();
    }

    public function getConfirmOrder(int $id, BaseHttpResponse $response, Request $request)
    {
        $order = app(OrderInterface::class)->createOrUpdate(
            [
                'status' => OrderStatusEnum::COMPLETED,
                'completed_at' => Carbon::now(),
            ],
            ['id' => $id]
        );

        $this->acceptedCommision($order);
        $orderDetails = OrderProduct::where('order_id', $id)->get();

        foreach ($orderDetails as $orderDetail) {
            $get_product = Product::find($orderDetail->product_id);
            $get_product->original_product->terjual = $get_product->original_product->terjual + $orderDetail->qty;
            $get_product->original_product->save();
        }
        $shipment = Shipment::where('order_id', $id)->first();

        event(new OrderCompletedEvent($order));
        // $action = do_action(ACTION_AFTER_ORDER_STATUS_COMPLETED_ECOMMERCE, $order, $request);
        $this->afterOrderStatusCompleted($order);

        app(OrderHistoryInterface::class)->createOrUpdate([
            'action' => 'update_status',
            'description' => 'Order confirmed',
            'order_id' => $shipment->order_id,
            'user_id' => auth('customer')->id(),
        ]);

        return $response->setMessage(trans('plugins/ecommerce::order.order_confirmed'));
    }

    private function acceptedCommision($go, $is_return = false)
    {
        DB::beginTransaction();
        try {
            $get_paket = MemberPaket::where('id', $go->id_paket)->with('paket')->first();
            $get_parent = Customer::where('id', $get_paket->parent)->first();

            $joinDate = Carbon::parse($get_paket->created_at);
            $expiredDate = Carbon::parse($get_paket->expire_date);
            $carbonNow = Carbon::now();
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $startDate = Carbon::now();

            if ($joinDate->month === $currentMonth && $joinDate->year === $currentYear) {
                $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $expiredDate->day)->subDay();
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

            $crete_commision_estimasi = new CommisionsEstimasi;
            $crete_commision_estimasi->id_paket = $go->id_paket;
            $crete_commision_estimasi->amount = ($go->amount - $go->shipping_amount) * ($get_paket->paket->fee_commissions / 100);
            $crete_commision_estimasi->status = 'pending';
            $crete_commision_estimasi->type = 'belanja';
            $crete_commision_estimasi->save();

            $sum_all_order = Order::where([
                ['user_id', $go->user_id],
                ['id_paket', $go->id_paket],
                // ['status', '<>', OrderStatusEnum::COMPLETED()],
                ['status', '<>', OrderStatusEnum::CANCELED()],
                ['status', '<>', OrderStatusEnum::RETURNED()]
            ])
                ->whereHas('payment', function ($query) {
                    $query->where('status', PaymentStatusEnum::COMPLETED());
                })
                ->whereDate('created_at', '>=', $previousDate)
                ->whereDate('created_at', '<=', $nextDate)
                ->selectRaw('SUM(amount - shipping_amount) as total_amount')
                ->first()
                ->total_amount;

            if (!$sum_all_order) {
                $sum_all_order = $go->amount - $go->shipping_amount;
            }
            // if ($sum_all_order) {
            //     if ((int)$sum_all_order < (int)$get_paket->paket->nominal) {
            //         $sum_all_order += $go->amount - $go->shipping_amount;
            //     }
            // } else {
            //     $sum_all_order = $go->amount - $go->shipping_amount;
            // }

            if ((int)$sum_all_order >= (int)$get_paket->paket->nominal) {
                $get_all_commisions_pending = CommisionsEstimasi::where([
                    ['id_paket', $go->id_paket],
                    ['status', 'pending']
                ])
                    ->whereNotIn('id', [$crete_commision_estimasi->id])
                    ->whereDate('created_at', '>=', $previousDate)
                    ->whereDate('created_at', '<=', $nextDate)
                    ->get();

                $commisions_fix = 0;
                // $commisions_fix = 0;
                foreach ($get_all_commisions_pending as $gacp) {
                    $commisions_fix += $gacp->amount;
                    $gacp->status = 'completed';
                    $gacp->save();
                }

                if ($get_parent) {
                    $totalWithdrawal = MemberWithdrawal::where('customer_id', $get_parent->id)
                        ->selectRaw('SUM(amount) as total_withdrawal')
                        ->first()
                        ->total_withdrawal;

                    if (!$totalWithdrawal) {
                        $totalWithdrawal = 0;
                    }

                    $get_parent->commissions_shopping += $commisions_fix;
                    $get_parent->commissions = ($get_parent->commissions_referral + $get_parent->commissions_shopping) - $totalWithdrawal;
                    // return $get_parent;
                    $get_parent->save();
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function getConfirmReturn(int $id, BaseHttpResponse $response, Request $request)
    {
        $returnRequest = $this->orderReturnRepository->findOrFail($id);
        $data['return_status'] = OrderReturnStatusEnum::COMPLETED;

        [$status, $returnRequest] = OrderReturnHelper::updateReturnOrder($returnRequest, $data);

        if (!$status) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.notices.update_return_order_status_error'));
        }

        $order = app(OrderInterface::class)->createOrUpdate(
            [
                'status' => OrderStatusEnum::COMPLETED,
                'completed_at' => Carbon::now(),
            ],
            ['id' => $returnRequest->order_id]
        );

        $this->acceptedCommision($order, true);
        $orderDetails = OrderProduct::where('order_id', $returnRequest->order_id)->get();

        foreach ($orderDetails as $orderDetail) {
            $get_product = Product::find($orderDetail->product_id);
            $get_product->original_product->terjual = $get_product->original_product->terjual + $orderDetail->qty;
            $get_product->original_product->save();
        }
        $shipment = Shipment::where('order_id', $returnRequest->order_id)->first();

        event(new OrderCompletedEvent($order));

        $this->afterOrderStatusCompleted($order);
        app(OrderHistoryInterface::class)->createOrUpdate([
            'action' => 'update_status',
            'description' => 'Order confirmed',
            'order_id' => $shipment->order_id,
            'user_id' => auth('customer')->id(),
        ]);

        return $response->setMessage(trans('plugins/ecommerce::order.order_confirmed'));
    }

    protected function afterOrderStatusCompleted(Order $order)
    {
        $order->loadMissing(['store', 'store.customer']);
        if ($order->store->id && $order->store->customer->id) {
            $customer = $order->store->customer;
            $vendorInfo = $customer->vendorInfo;

            if (!$vendorInfo->id) {
                $vendorInfo = App::make(VendorInfoInterface::class)
                    ->createOrUpdate([
                        'customer_id' => $customer->id,
                    ]);
            }

            if ($vendorInfo->id) {
                $revenue = App::make(RevenueInterface::class)->getFirstBy(['order_id' => $order->id]);
                $orderAmountWithoutShippingFee = $order->amount - $order->shipping_amount;

                if (!MarketplaceHelper::isCommissionCategoryFeeBasedEnabled()) {
                    $feePercentage = MarketplaceHelper::getSetting('fee_per_order', 0);
                    $fee = $orderAmountWithoutShippingFee * ($feePercentage / 100);
                } else {
                    $fee = $this->calculatorCommissionFeeByProduct($order->products);
                }

                $amount = $orderAmountWithoutShippingFee - $fee;
                $currentBalance = $customer->balance;

                $amountByCurrency = $amount;
                $revenueAmount = $revenue ? $revenue->amount : 0;

                $data = [
                    'sub_amount' => $orderAmountWithoutShippingFee,
                    'fee' => $fee,
                    'amount' => $amount,
                    'id_paket' => $order->id_paket,
                    'currency' => get_application_currency()->title,
                    'current_balance' => $currentBalance,
                    'customer_id' => $customer->getKey(),
                ];
                DB::beginTransaction();
                try {

                    if ($revenue) {
                        $amountByCurrency -= $revenueAmount;
                        $fee = 0;
                        $data['current_balance'] = $currentBalance - $revenueAmount;
                        $revenue->fill($data);
                        $revenue->save();
                    } else {
                        App::make(RevenueInterface::class)->createOrUpdate(array_merge([
                            'order_id' => $order->id,
                        ], $data));

                        $vendorInfo->total_revenue += $amountByCurrency;
                    }
                    $vendorInfo->balance += $amountByCurrency;
                    $vendorInfo->total_fee += $fee;
                    $vendorInfo->save();
                    DB::commit();
                } catch (Throwable | Exception $th) {
                    DB::rollBack();
                    return (new BaseHttpResponse())
                        ->setError()
                        ->setMessage($th->getMessage());
                }
            }
        }
    }

    protected function calculatorCommissionFeeByProduct(Collection $orderProducts): float|int
    {
        $totalFee = 0;
        foreach ($orderProducts as $orderProduct) {
            $product = $orderProduct->product->original_product;

            if (!$product) {
                continue;
            }

            $listCategories = $product->categories()->pluck('category_id')->all();

            $commissionFeePercentage = MarketplaceHelper::getSetting('fee_per_order', 0);
            $commissionSetting = CategoryCommission::whereIn('product_category_id', $listCategories)
                ->orderBy('commission_percentage', 'desc')
                ->first();

            if (!empty($commissionSetting)) {
                $commissionFeePercentage = $commissionSetting->commission_percentage;
            }

            $totalFee += $orderProduct->price * $commissionFeePercentage / 100;
        }

        return $totalFee;
    }

    public function getCancelOrder(int $id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->getFirstBy([
            'id' => $id,
            'user_id' => auth('customer')->id(),
        ], ['*']);

        if (!$order) {
            abort(404);
        }

        if (!$order->canBeCanceled()) {
            return $response->setError()
                ->setMessage(trans('plugins/ecommerce::order.cancel_error'));
        }

        OrderHelper::cancelOrder($order);

        $this->orderHistoryRepository->createOrUpdate([
            'action' => 'cancel_order',
            'description' => __('Order was cancelled by custom :customer', ['customer' => $order->address->name]),
            'order_id' => $order->id,
        ]);

        return $response->setMessage(trans('plugins/ecommerce::order.cancel_success'));
    }

    public function getListAddresses(Request $request)
    {
        SeoHelper::setTitle(__('Address books'));

        $addresses = $this->addressRepository->advancedGet([
            'condition' => [
                'customer_id' => auth('customer')->id(),
            ],
            'order_by' => [
                'is_default' => 'DESC',
                'created_at' => 'DESC',
            ],
            'paginate' => [
                'per_page' => 10,
                'current_paged' => (int)$request->input('page', 1),
            ],
        ]);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Address books'), route('customer.address'));

        return Theme::scope(
            'ecommerce.customers.address.list',
            compact('addresses'),
            'plugins/ecommerce::themes.customers.address.list'
        )->render();
    }

    public function getCreateAddress()
    {
        SeoHelper::setTitle(__('Create Address'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Address books'), route('customer.address'))
            ->add(__('Create Address'), route('customer.address.create'));

        return Theme::scope(
            'ecommerce.customers.address.create',
            [],
            'plugins/ecommerce::themes.customers.address.create'
        )->render();
    }

    public function postCreateAddress(AddressRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default') == 1) {
            $this->addressRepository->update([
                'is_default' => 1,
                'customer_id' => auth('customer')->id(),
            ], ['is_default' => 0]);
        }

        $request->merge([
            'customer_id' => auth('customer')->id(),
            'is_default' => $request->input('is_default', 0),
        ]);

        $address = $this->addressRepository->createOrUpdate($request->input());

        return $response
            ->setData([
                'id' => $address->id,
                'html' => view(
                    'plugins/ecommerce::orders.partials.address-item',
                    compact('address')
                )->render(),
            ])
            ->setNextUrl(route('customer.address'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function getEditAddress(int $id)
    {
        SeoHelper::setTitle(__('Edit Address #:id', ['id' => $id]));

        $address = $this->addressRepository->getFirstBy([
            'id' => $id,
            'customer_id' => auth('customer')->id(),
        ]);

        if (!$address) {
            abort(404);
        }

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Edit Address #:id', ['id' => $id]), route('customer.address.edit', $id));

        return Theme::scope(
            'ecommerce.customers.address.edit',
            compact('address'),
            'plugins/ecommerce::themes.customers.address.edit'
        )->render();
    }

    public function getDeleteAddress(int $id, BaseHttpResponse $response)
    {
        $this->addressRepository->deleteBy([
            'id' => $id,
            'customer_id' => auth('customer')->id(),
        ]);

        return $response->setNextUrl(route('customer.address'))
            ->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function postEditAddress(int $id, AddressRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default')) {
            $this->addressRepository->update([
                'is_default' => 1,
                'customer_id' => auth('customer')->id(),
            ], ['is_default' => 0]);
        }

        $address = $this->addressRepository->createOrUpdate($request->input(), [
            'id' => $id,
            'customer_id' => auth('customer')->id(),
        ]);

        return $response
            ->setData([
                'id' => $address->id,
                'html' => view('plugins/ecommerce::orders.partials.address-item', compact('address'))
                    ->render(),
            ])
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getPrintOrder(int $id, Request $request)
    {
        $order = $this->orderRepository->getFirstBy([
            'id' => $id,
            'user_id' => auth('customer')->id(),
        ]);

        if (!$order || !$order->isInvoiceAvailable()) {
            abort(404);
        }

        if ($request->input('type') == 'print') {
            return InvoiceHelper::streamInvoice($order->invoice);
        }

        return InvoiceHelper::downloadInvoice($order->invoice);
    }

    public function postAvatar(AvatarRequest $request, ThumbnailService $thumbnailService, BaseHttpResponse $response)
    {
        try {
            $account = auth('customer')->user();

            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, 'customers');

            if ($result['error']) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(RvMedia::getRealPath($file->url))
                ->setSize((int)$avatarData->width, (int)$avatarData->height)
                ->setCoordinates((int)$avatarData->x, (int)$avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('crop');

            $account->avatar = $file->url;

            $this->customerRepository->createOrUpdate($account);

            return $response
                ->setMessage(trans('plugins/customer::dashboard.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getReturnOrder(int $orderId)
    {
        $order = $this->orderRepository->getFirstBy(
            [
                'id' => $orderId,
                'user_id' => auth('customer')->id(),
                'status' => OrderStatusEnum::PROCESSING(),
            ],
            ['ec_orders.*'],
            ['products']
        );

        // if (!$order || !$order->canBeReturned()) {
        // if (!$order) {
        //     abort(404);
        // }

        SeoHelper::setTitle(__('Request Return Product(s) In Order :id', ['id' => $order->code]));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(
                __('Request Return Product(s) In Order :id', ['id' => $order->code]),
                route('customer.order_returns.request_view', $orderId)
            );

        Theme::asset()->container('footer')->add(
            'order-return-js',
            'vendor/core/plugins/ecommerce/js/order-return.js',
            ['jquery']
        );
        Theme::asset()->add('order-return-css', 'vendor/core/plugins/ecommerce/css/order-return.css');

        return Theme::scope(
            'ecommerce.customers.order-returns.view',
            compact('order'),
            'plugins/ecommerce::themes.customers.order-returns.view'
        )->render();
    }

    public function postReturnOrder(OrderReturnRequest $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->getFirstBy([
            'id' => $request->input('order_id'),
            'user_id' => auth('customer')->id(),
        ]);

        if (!$order) {
            abort(404);
        }

        if (!$order->canBeReturned()) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage(trans('plugins/ecommerce::order.return_error'));
        }

        $orderReturnData['reason'] = $request->input('reason');

        $orderReturnData['items'] = Arr::where($request->input(['return_items']), function ($value) {
            return isset($value['is_return']);
        });

        $order->status = OrderStatusEnum::RETURNED();
        $order->save();

        if (empty($orderReturnData['items'])) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage(__('Please select at least 1 product to return!'));
        }

        [$status, $data, $message] = OrderReturnHelper::returnOrder($order, $orderReturnData);

        if (!$status) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage($message ?: trans('plugins/ecommerce::order.return_error'));
        }

        $this->orderHistoryRepository->createOrUpdate([
            'action' => 'return_order',
            'description' => __(':customer has requested return product(s)', ['customer' => $order->address->name]),
            'order_id' => $order->id,
        ]);

        return $response
            ->setMessage(trans('plugins/ecommerce::order.return_success'))
            ->setNextUrl(route('customer.order_returns.detail', ['id' => $data->id]));
    }

    public function getListReturnOrders(Request $request)
    {
        SeoHelper::setTitle(__('Order Return Requests'));

        $requests = $this->orderReturnRepository->advancedGet([
            'condition' => [
                'user_id' => auth('customer')->id(),
            ],
            'paginate' => [
                'per_page' => 10,
                'current_paged' => (int)$request->input('page'),
            ],
            'withCount' => ['items'],
            'order_by' => ['created_at' => 'DESC'],
        ]);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Order Return Requests'), route('customer.order_returns'));

        return Theme::scope(
            'ecommerce.customers.order-returns.list',
            compact('requests'),
            'plugins/ecommerce::themes.customers.orders.returns.list'
        )->render();
    }

    public function getDetailReturnOrder(int $id)
    {
        SeoHelper::setTitle(__('Order Return Requests'));

        $orderReturn = $this->orderReturnRepository->getFirstBy([
            'id' => $id,
            'user_id' => auth('customer')->id(),
        ]);

        if (!$orderReturn) {
            abort(404);
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Order Return Requests'), route('customer.order_returns'))
            ->add(
                __('Order Return Requests :id', ['id' => $orderReturn->id]),
                route('customer.order_returns.detail', $orderReturn->id)
            );

        return Theme::scope(
            'ecommerce.customers.order-returns.detail',
            compact('orderReturn'),
            'plugins/ecommerce::themes.customers.order-returns.detail'
        )->render();
    }

    public function getDownloads()
    {
        if (!EcommerceHelper::isEnabledSupportDigitalProducts()) {
            abort(404);
        }

        SeoHelper::setTitle(__('Downloads'));

        $orderProducts = $this->orderProductRepository->getModel()
            ->whereHas('order', function ($query) {
                $query->where([
                    'user_id' => auth('customer')->id(),
                    'is_finished' => 1,
                ]);
            })
            ->whereHas('order.payment', function ($query) {
                $query->where(['status' => PaymentStatusEnum::COMPLETED]);
            })
            ->where('product_type', ProductTypeEnum::DIGITAL)
            ->orderBy('created_at', 'desc')
            ->with(['order', 'product'])
            ->paginate(10);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Downloads'), route('customer.downloads'));

        return Theme::scope(
            'ecommerce.customers.orders.downloads',
            compact('orderProducts'),
            'plugins/ecommerce::themes.customers.orders.downloads'
        )->render();
    }

    public function getDownload(int $id, BaseHttpResponse $response)
    {
        if (!EcommerceHelper::isEnabledSupportDigitalProducts()) {
            abort(404);
        }

        $orderProduct = $this->orderProductRepository->getModel()
            ->where([
                'id' => $id,
                'product_type' => ProductTypeEnum::DIGITAL,
            ])
            ->whereHas('order', function ($query) {
                $query->where([
                    'user_id' => auth('customer')->id(),
                    'is_finished' => 1,
                ]);
            })
            ->whereHas('order.payment', function ($query) {
                $query->where(['status' => PaymentStatusEnum::COMPLETED]);
            })
            ->with(['order', 'product'])
            ->first();

        if (!$orderProduct) {
            abort(404);
        }

        $zipName = 'digital-product-' . Str::slug($orderProduct->product_name) . Str::random(5) . '-' . Carbon::now()->format('Y-m-d-h-i-s') . '.zip';
        $fileName = RvMedia::getRealPath($zipName);
        $zip = new Zipper();
        $zip->make($fileName);
        $product = $orderProduct->product;
        $productFiles = $product->id ? $product->productFiles : $orderProduct->productFiles;

        if (!$productFiles->count()) {
            return $response->setError()->setMessage(__('Cannot found files'));
        }
        foreach ($productFiles as $file) {
            $filePath = RvMedia::getRealPath($file->url);
            if (!RvMedia::isUsingCloud()) {
                if (File::exists($filePath)) {
                    $zip->add($filePath);
                }
            } else {
                $zip->addString(
                    $file->file_name,
                    file_get_contents(str_replace('https://', 'http://', $filePath))
                );
            }
        }

        if (version_compare(phpversion(), '8.0') >= 0) {
            $zip = null;
        } else {
            $zip->close();
        }

        if (File::exists($fileName)) {
            $orderProduct->increment('times_downloaded');

            return response()->download($fileName)->deleteFileAfterSend();
        }

        return $response->setError()->setMessage(__('Cannot download files'));
    }

    public function getProductReviews()
    {
        if (!EcommerceHelper::isReviewEnabled()) {
            abort(404);
        }

        SeoHelper::setTitle(__('Product Reviews'));

        Theme::asset()
            ->add('ecommerce-review-css', 'vendor/core/plugins/ecommerce/css/review.css');
        Theme::asset()->container('footer')
            ->add('ecommerce-review-js', 'vendor/core/plugins/ecommerce/js/review.js', ['jquery']);

        $customerId = auth('customer')->id();

        $reviews = $this->reviewRepository
            ->getModel()
            ->where('customer_id', $customerId)
            ->whereHas('product', function ($query) {
                $query->where('status', BaseStatusEnum::PUBLISHED);
            })
            ->with(['product', 'product.slugable'])
            ->orderBy('ec_reviews.created_at', 'desc')
            ->paginate(12);

        $products = $this->productRepository->productsNeedToReviewByCustomer($customerId);

        foreach ($reviews as $review) {
            $get_parent = $this->reviewRepository->getModel()->where('parent_id', $review->id)->get();
            if (count($get_parent) > 0) {
                $vendor = array();
                foreach ($get_parent as $parent) {
                    $get_vendor = \DB::select("SELECT * FROM mp_stores WHERE id = '$parent->parent_id'")[0];
                    if ($get_vendor) {
                        $vendor = $get_vendor;
                    }
                    $parent->vendor = (object)$vendor;
                }
            }
            $review->parent = $get_parent;
        }
        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Product Reviews'), route('customer.product-reviews'));

        return Theme::scope(
            'ecommerce.customers.product-reviews.list',
            compact('products', 'reviews'),
            'plugins/ecommerce::themes.customers.product-reviews.list'
        )->render();
    }

    public function getMemberDownline(Request $request, BaseHttpResponse $response)
    {
        if ($request->cancel) {
            $data = json_decode(base64_decode($request->cancel));
            $member = auth('customer')->user();

            $memberWithdrawal = MemberWithdrawal::where('id', $data->id)->first();
            $memberWithdrawal->status = WithdrawalStatusEnum::CANCELED;
            if ($memberWithdrawal->save()) {
                $member->commissions += (int)$memberWithdrawal->amount;
                $member->save();
            }

            return $response
                ->setPreviousUrl(route('customer.member-list'))
                ->setMessage(trans('core/base::notices.update_success_message'));
        }
        SeoHelper::setTitle(__('Member Downline'));

        $members = [];
        $member_list = [];
        $parent_paket = MemberPaket::where('user_id', auth('customer')->id())->get();

        $customer_member = MemberPaket::where('parent', auth('customer')->id())->get();
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

        // return $paket_customer;
        $members = $paket_customer->groupBy('id_paket')->map(function ($items) {
            $firstItem = $items->first();
            return [
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

                    // return [$previousDate,$nextDate,$item->customer->id,$expiredDate,$item->customer->name];
                    $total_belanja = Order::where([
                        ['user_id', $item->customer->id],
                        ['id_paket', $item->id],
                        ['status', OrderStatusEnum::COMPLETED()],
                        // ['status', '<>', OrderStatusEnum::CANCELED()],
                        // ['status', '<>', OrderStatusEnum::RETURNED()]
                    ])
                        ->whereHas('payment', function ($query) {
                            $query->where('status', PaymentStatusEnum::COMPLETED());
                        })
                        ->whereDate('created_at', '>=', $previousDate)
                        ->whereDate('created_at', '<=', $nextDate)
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

        // return $members;
        $total_witdrawal = MemberWithdrawal::where([['customer_id', auth('customer')->user()->id], ['status', WithdrawalStatusEnum::COMPLETED()]])->sum('amount');
        $get_wihdrawal = MemberWithdrawal::where('customer_id', auth('customer')->user()->id)->get();

        $is_member = MemberPaket::where('user_id', auth('customer')->id())->with('customer')->first();

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Member Downline'), route('customer.member-list'));

        return Theme::scope(
            'ecommerce.customers.member-list.list',
            compact('members', 'get_wihdrawal', 'is_member', 'total_witdrawal'),
            'plugins/ecommerce::themes.customers.member-list.list'
        )->render();
    }

    public function storeWithdrawal(MemberWithdrawalRequest $request, BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $member = auth('customer')->user();

            $memberWithdrawal = new MemberWithdrawal;
            $memberWithdrawal->amount = (int)$request->input('amount');
            $memberWithdrawal->description = $request->input('description');
            $memberWithdrawal->currency = get_application_currency()->title;
            $memberWithdrawal->bank_info = $request->input('bank_info');
            $memberWithdrawal->payment_channel = \Botble\Marketplace\Enums\PayoutPaymentMethodsEnum::BANK_TRANSFER;
            $memberWithdrawal->customer_id = $member->id;
            $memberWithdrawal->current_balance = $member->commissions;
            if ($memberWithdrawal->save()) {
                $member->commissions -= (int)$request->input('amount');
                $member->save();
                DB::commit();
            }
        } catch (Throwable | Exception $th) {
            DB::rollBack();

            return $response
                ->setError()
                ->setMessage($th->getMessage());
        }

        return $response
            ->setPreviousUrl(route('customer.member-list'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }
    public function getChat()
    {
        SeoHelper::setTitle(__('Chat'));

        return Theme::scope(
            'ecommerce.customers.chat',
            [],
            'plugins/ecommerce::themes.customers.chat'
        )->render();
    }

    public function uploadPayment(Request $request, BaseHttpResponse $response)
    {
        \DB::beginTransaction();
        try {
            $get_payment = Payment::where('id', base64_decode($request->payment_id))->first();
            if ($get_payment) {
                $get_payment->status = PaymentStatusEnum::PENDING;
                if ($get_payment->save()) {
                    $file = $request->file('file');
                    $result = (object)\RvMedia::handleUpload($file, 0, 'payment-verifikasi');
                    if (!$result->error) {
                        $create_payment_file = new PaymentFile;
                        $create_payment_file->payment_id = base64_decode($request->payment_id);
                        $create_payment_file->image = $result->data->url;
                        $create_payment_file->bank_holder = $request->nama_pengirim;
                        $create_payment_file->bank_number = $request->nomor_rekening;
                        if ($create_payment_file->save()) {
                            \DB::commit();
                            return $response->setData($create_payment_file);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return $response->setError(true)->setData($e->getMessage());
        }
    }

    public function ahliWaris()
    {
        SeoHelper::setTitle(__('Ahli Waris'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Ahli Waris'), route('customer.ahli-waris'));

        $ahliWariss = AhliWaris::where('customer_id', auth('customer')->id())->get();

        foreach ($ahliWariss as $ahliWaris) {
            $ahliWaris->alamat_ktp = json_decode(decrypt_data_ahli_waris($ahliWaris->alamat_ktp, base64_decode($ahliWaris->uuid)));
            $ahliWaris->alamat_tinggal = json_decode(decrypt_data_ahli_waris($ahliWaris->alamat_tinggal, base64_decode($ahliWaris->uuid)));
            $ahliWaris->nik = decrypt_data_ahli_waris($ahliWaris->nik, base64_decode($ahliWaris->uuid));

            $ahliWaris->nik = hidden_nik($ahliWaris->nik);
        }

        return Theme::scope(
            'ecommerce.customers.ahli-waris',
            compact('ahliWariss'),
            'plugins/ecommerce::themes.customers.ahli-waris.index'
        )->render();
    }

    public function createAhliWaris()
    {
        SeoHelper::setTitle(__('Ahli Waris'));

        $getCustomerAhliWaris = AhliWaris::where('customer_id', auth('customer')->id())->count();

        if ($getCustomerAhliWaris >= 5) {
            return redirect(route('customer.ahli-waris'))->with('message-member', "Data ahli waris anda sudah mencapai batas!!");
        }
        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Ahli Waris'), route('customer.ahli-waris'));

        return Theme::scope(
            'ecommerce.customers.ahli-waris',
            [],
            'plugins/ecommerce::themes.customers.ahli-waris.create'
        )->render();
    }

    public function editAhliWaris(int $id)
    {
        SeoHelper::setTitle(__('Ahli Waris'));

        $ahliWaris = AhliWaris::find($id);

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Ahli Waris'), route('customer.ahli-waris'));

        $ahliWaris->nik = decrypt_data_ahli_waris($ahliWaris->nik, base64_decode($ahliWaris->uuid));
        $ahliWaris->alamat_ktp = json_decode(decrypt_data_ahli_waris($ahliWaris->alamat_ktp, base64_decode($ahliWaris->uuid)));
        $ahliWaris->alamat_tinggal = json_decode(decrypt_data_ahli_waris($ahliWaris->alamat_tinggal, base64_decode($ahliWaris->uuid)));
        // return $ahliWaris;
        return Theme::scope(
            'ecommerce.customers.ahli-waris',
            compact('ahliWaris'),
            'plugins/ecommerce::themes.customers.ahli-waris.create'
        )->render();
    }

    public function deleteAhliWaris(int $id, BaseHttpResponse $response)
    {
        AhliWaris::where([
            ['customer_id', auth('customer')->id()],
            ['id', $id]
        ])->delete();
        return $response->setNextUrl(route('customer.address'))
            ->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function storeAhliWaris($id, Request $request)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        if ($id !== 'create') {
            $createData = AhliWaris::find($id);
        } else {
            $createData = new AhliWaris;
            $createData->customer_id = auth('customer')->id();
        }
        $createData->name = $request->name;
        $createData->phone = $request->phone;
        $createData->uuid = base64_encode($iv);
        $createData->nik = encrypt_data_ahli_waris($request->nik, $iv);
        if ($request->same_ktp === '1') {
            $residence = json_encode([
                'alamat_tinggal' => $request->alamat_ktp,
                'kota_tinggal' => $request->kota_ktp,
                'kecamatan_tinggal' => $request->kecamatan_ktp,
                'provinsi_tinggal' => $request->provinsi_ktp,
            ]);

            $alamat_ktp = json_encode([
                'alamat_ktp' => $request->alamat_ktp,
                'kota_ktp' => $request->kota_ktp,
                'kecamatan_ktp' => $request->kecamatan_ktp,
                'provinsi_ktp' => $request->provinsi_ktp,
            ]);
        } else {
            $residence = json_encode([
                'alamat_tinggal' => $request->alamat_tinggal,
                'kota_tinggal' => $request->kota_tinggal,
                'kecamatan_tinggal' => $request->kecamatan_tinggal,
                'provinsi_tinggal' => $request->provinsi_tinggal,
            ]);

            $alamat_ktp = json_encode([
                'alamat_ktp' => $request->alamat_ktp,
                'kota_ktp' => $request->kota_ktp,
                'kecamatan_ktp' => $request->kecamatan_ktp,
                'provinsi_ktp' => $request->provinsi_ktp,
            ]);
        }
        $createData->alamat_ktp = encrypt_data_ahli_waris($alamat_ktp, $iv);
        $createData->alamat_tinggal = encrypt_data_ahli_waris($residence, $iv);
        $getCustomerAhliWaris = AhliWaris::where('customer_id', $createData->customer_id)->first();
        $createData->is_primary = ($getCustomerAhliWaris) ? 0 : 1;
        if ($createData->save()) {
            if ($id !== null) {
                return redirect(route('customer.ahli-waris'))->with('message-member', "Data berhasil di update");
            }
            return redirect(route('customer.ahli-waris'))->with('message-member', "Data berhasil di simpan");
        } else {
            return redirect(route('customer.ahli-waris'))->with('message-member', "Terjadi kesalahan. Silahkan coba lagi");
        }
    }

    public function withdrawalShow(int $id, FormBuilder $formBuilder)
    {
        $withdrawal = MemberWithdrawal::where([
            ['id', '=', $id],
            ['customer_id', '=', auth('customer')->id()],
            ['status', '!=', WithdrawalStatusEnum::PENDING],
        ])->first();

        if (!$withdrawal) {
            abort(404);
        }

        page_title()->setTitle(__('View withdrawal request #' . $id));
        return Theme::scope(
            'ecommerce.customers.member-list.list',
            compact('withdrawal'),
            'plugins/ecommerce::themes.customers.member-list.show'
        )->render();
    }

    public function withdrawalShowForm(int $id, FormBuilder $formBuilder)
    {
        $withdrawal = MemberWithdrawal::where([
            ['id', '=', $id],
            ['customer_id', '=', auth('customer')->id()],
            ['status', '!=', WithdrawalStatusEnum::PENDING],
        ])->first();

        if (!$withdrawal) {
            abort(404);
        }

        return $formBuilder->create(ShowMemberWithdrawalForm::class, ['model' => $withdrawal])->renderForm();
    }

    public function handleReadAndDirect(int $id, Request $request)
    {
        $get_notification = Broadcast::where('id', $id)->first();
        if ($get_notification) {
            DB::table('broadcast_reads')->updateOrInsert([
                'customer_id' => auth('customer')->id(),
                'broadcast_id' => $get_notification->id,
            ]);

            if ($get_notification->type === 'product') {
                $condition = [
                    'ec_products.id' => $get_notification->product_id,
                    'ec_products.status' => BaseStatusEnum::PUBLISHED,
                ];

                $product = get_products(array_merge([
                    'condition' => $condition,
                    'take' => 1,
                    'with' => [
                        'slugable',
                        'tags',
                        'tags.slugable',
                        'categories',
                        'categories.slugable',
                        'options',
                        'options.values',
                    ],
                ]));

                return redirect($product->url);
            } else {
                return redirect($get_notification->website);
            }
        }
        return $get_notification;
    }

    public function bantuan()
    {
        SeoHelper::setTitle(__('Bantuan'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Bantuan'), route('customer.bantuan'));

        $messages = SupportMessage::where('from', auth('customer')->id())
            ->orWhere('for', auth('customer')->id())
            ->orderBy('created_at', 'ASC')
            ->get();

        // return $messages;
        return Theme::scope(
            'ecommerce.customers.bantuan',
            compact('messages'),
            'plugins/ecommerce::themes.customers.bantuan.index'
        )->render();
    }

    public function storeBantuan(Request $request)
    {
        $createMessage = new SupportMessage;
        $createMessage->from = auth('customer')->id();
        $createMessage->message = $request->message;
        $createMessage->save();

        return redirect()->back();
    }
}
