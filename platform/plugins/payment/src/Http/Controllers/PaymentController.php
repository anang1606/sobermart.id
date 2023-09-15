<?php

namespace Botble\Payment\Http\Controllers;

use Assets;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\JneOrigin;
use Botble\Ecommerce\Models\JneSupport;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\PaymentFile;
use Botble\Ecommerce\Models\BankList;
use Botble\Ecommerce\Models\CommisionsEstimasi;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\MemberWithdrawal;
use Botble\Ecommerce\Models\Shipment;
use Botble\Marketplace\Models\Store;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Http\Requests\PaymentMethodRequest;
use Botble\Payment\Http\Requests\UpdatePaymentRequest;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Botble\Payment\Tables\PaymentTable;
use Botble\Payment\Tables\PaymentTablePaket;
use Botble\Payment\Tables\BankTable;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use MarketplaceHelper;

class PaymentController extends Controller
{
    protected PaymentInterface $paymentRepository;

    public function __construct(PaymentInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(PaymentTable $table)
    {
        page_title()->setTitle(trans('plugins/payment::payment.name'));

        return $table->renderTable();
    }

    public function paket(PaymentTablePaket $table)
    {
        page_title()->setTitle('Paket');

        return $table->renderTable();
    }

    public function bank(BankTable $table)
    {
        page_title()->setTitle('Bank List');

        return $table->renderTable();
    }

    public function createBank()
    {
        page_title()->setTitle('Bank Create');

        return view('plugins/payment::bank.create');
    }

    public function editBank($id)
    {
        page_title()->setTitle('Bank Edit');

        $bank_list = BankList::where('id', $id)->first();

        return view('plugins/payment::bank.edit', compact('bank_list'));
    }

    public function storeBank(Request $request, BaseHttpResponse $response)
    {
        $file = $request->file('file');
        $result = (object)\RvMedia::handleUpload($file, 0, 'icon-bank');

        $create_bank = new BankList;
        $create_bank->bank_code = $request->bank_code;
        $create_bank->bank_holder = $request->bank_holder;
        $create_bank->bank_nomor = $request->bank_nomor;
        $create_bank->bank_name = $request->bank_name;
        $create_bank->fee = $request->fee;
        $create_bank->icons = $result->data->url;

        $create_bank->save();
        return $response
            ->setPreviousUrl(route('bank.show', $create_bank->id))
            ->setMessage('Save data success');
    }

    public function updateBank(Request $request, BaseHttpResponse $response, $id)
    {
        $bank_list = BankList::where('id', $id)->first();

        if ($bank_list) {
            $file = $request->file('file');

            $bank_list->bank_code = $request->bank_code;
            $bank_list->bank_holder = $request->bank_holder;
            $bank_list->bank_nomor = $request->bank_nomor;
            $bank_list->bank_name = $request->bank_name;
            $bank_list->fee = $request->fee;

            if ($file) {
                $result = (object)\RvMedia::handleUpload($file, 0, 'icon-bank');
                $bank_list->icons = $result->data->url;
            }
            $bank_list->save();
            return $response
                ->setPreviousUrl(route('bank.show', $bank_list->id))
                ->setMessage('Save data success');
        }
    }

    public function destroyBank(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            BankList::find($id)->delete();
            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $payment = $this->paymentRepository->findOrFail($id);

            $this->paymentRepository->delete($payment);

            event(new DeletedContentEvent(PAYMENT_MODULE_SCREEN_NAME, $request, $payment));

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
            $payment = $this->paymentRepository->findOrFail($id);
            $this->paymentRepository->delete($payment);
            event(new DeletedContentEvent(PAYMENT_MODULE_SCREEN_NAME, $request, $payment));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function show($id)
    {
        $payment = $this->paymentRepository->findOrFail($id);

        page_title()->setTitle(trans('plugins/payment::payment.view_transaction', ['charge_id' => $payment->charge_id]));

        $detail = apply_filters(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, null, $payment);

        $paymentStatuses = PaymentStatusEnum::labels();

        if ($payment->status != PaymentStatusEnum::PENDING) {
            Arr::forget($paymentStatuses, PaymentStatusEnum::PENDING);
        }

        $orders = Order::where('payment_id', $id)->get();
        $totalShipmentJne = 0;
        $totalShipmentJnt = 0;
        $subAmount = 0;
        foreach ($orders as $order) {
            $subAmount = $order->amount - $order->shipping_amount;
            if (strpos($order->shipping_service, 'J&T') !== false) {
                $totalShipmentJnt += $order->shipping_amount;
            } else {
                $totalShipmentJne += $order->shipping_amount;
            }
        }
        $payment_verifs = PaymentFile::where('payment_id', $id)->get();

        $payment->totalShipmentJne = $totalShipmentJne;
        $payment->totalShipmentJnt = $totalShipmentJnt;
        $payment->subAmount = $subAmount;
        // return $order;
        Assets::addScriptsDirectly('vendor/core/plugins/payment/js/payment-detail.js');

        return view('plugins/payment::show', compact('payment', 'detail', 'payment_verifs', 'paymentStatuses'));
    }

    public function methods()
    {
        page_title()->setTitle(trans('plugins/payment::payment.payment_methods'));

        Assets::addStylesDirectly('vendor/core/plugins/payment/css/payment-methods.css')
            ->addScriptsDirectly('vendor/core/plugins/payment/js/payment-methods.js');

        return view('plugins/payment::settings.index');
    }

    public function updateSettings(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        $data = $request->except(['_token']);
        foreach ($data as $settingKey => $settingValue) {
            $settingStore
                ->set($settingKey, $settingValue);
        }

        $settingStore->save();

        return $response->setMessage(trans('plugins/payment::payment.saved_payment_settings_success'));
    }

    public function updateMethods(PaymentMethodRequest $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        $type = $request->input('type');
        $data = $request->except(['_token', 'type']);
        foreach ($data as $settingKey => $settingValue) {
            $settingStore
                ->set($settingKey, $settingValue);
        }

        $settingStore
            ->set('payment_' . $type . '_status', 1)
            ->save();

        return $response->setMessage(trans('plugins/payment::payment.saved_payment_method_success'));
    }

    public function updateMethodStatus(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        $settingStore
            ->set('payment_' . $request->input('type') . '_status', 0)
            ->save();

        return $response->setMessage(trans('plugins/payment::payment.turn_off_success'));
    }

    public function update($id, UpdatePaymentRequest $request, BaseHttpResponse $response)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::find($id);
            $payment->status = $request->input('status');
            $payment->notes = $request->input('notes');
            $payment->save();
            if ($request->input('status') === 'completed') {
                if ($payment->status !== PaymentStatusEnum::COMPLETED) {
                    if ($payment->type_status === 'paket') {
                        $checkMember = MemberPaket::where('id', $payment->order_id)->whereNotNull('expire_date')->with('paket')->first();
                        if ($checkMember) {
                            $checkMember->expire_date = Carbon::parse($checkMember->expire_date)->addYear();
                            $checkMember->save();
                        } else {
                            $check_commissions = CommisionsEstimasi::where([['id_paket', $payment->order_id], ['type', 'paket'], ['status', 'completed']])->first();
                            if (!$check_commissions) {
                                $get_member_paket = MemberPaket::where('id', $payment->order_id)->with('paket')->first();
                                if ($get_member_paket) {
                                    $get_member_paket->expire_date = Carbon::now()->addYear();
                                    $get_parent = Customer::where('id', $get_member_paket->parent)->first();
                                    if ($get_parent) {

                                        $totalWithdrawal = MemberWithdrawal::where('customer_id', $get_parent->id)
                                        ->selectRaw('SUM(amount) as total_withdrawal')
                                        ->first()
                                        ->total_withdrawal;

                                        if (!$totalWithdrawal) {
                                            $totalWithdrawal = 0;
                                        }
                                        $get_parent->commissions_referral += $get_member_paket->paket->nominal * (MarketplaceHelper::getSetting('pendapatan_pribadi', 0) / 100);
                                        $get_parent->commissions = ($get_parent->commissions_referral + $get_parent->commissions_shopping) - $totalWithdrawal;
                                        $get_parent->save();

                                        $crete_commision_estimasi = new CommisionsEstimasi;
                                        $crete_commision_estimasi->id_paket = $payment->order_id;
                                        $crete_commision_estimasi->amount = $get_parent->commissions;
                                        $crete_commision_estimasi->status = 'completed';
                                        $crete_commision_estimasi->type = 'paket';
                                        $crete_commision_estimasi->save();
                                    }
                                    $get_member_paket->save();
                                }
                            }
                        }
                    } else {
                        // $get_order = Order::where('payment_id', $payment->id)->get();
                        // $bulanIni = Carbon::now()->startOfMonth();
                        // foreach ($get_order as $go) {
                        //     if ($go->id_paket !== null) {
                        //         $sum_all_order = 0;
                        //         $get_paket = MemberPaket::where('id', $go->id_paket)->with('paket')->first();
                        //         // $get_user = Customer::where('id', $get_paket->user_id)->first();
                        //         $get_parent = Customer::where('id', $get_paket->parent)->first();

                        //         $joinDate = Carbon::parse($get_paket->created_at);
                        //         $expiredDate = Carbon::parse($get_paket->expire_date);
                        //         $carbonNow = Carbon::now();
                        //         $currentMonth = Carbon::now()->month;
                        //         $currentYear = Carbon::now()->year;
                        //         $startDate = Carbon::now();

                        //         if ($joinDate->month === $currentMonth && $joinDate->year === $currentYear) {
                        //             $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $expiredDate->day)->subDay();
                        //             $nextDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day)->addMonth();
                        //         } else {
                        //             // $endDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day);
                        //             if ($expiredDate->isBefore($startDate)) {
                        //                 $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $expiredDate->day);
                        //                 $nextDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day)->addMonth();
                        //             } else {
                        //                 $previousDate = Carbon::create($carbonNow->year, $carbonNow->month, $expiredDate->day)->subMonth();
                        //                 $nextDate = Carbon::create($carbonNow->year, Carbon::now()->month, $expiredDate->day);
                        //             }
                        //         }

                        //         // $crete_commision_estimasi = new CommisionsEstimasi;
                        //         // $crete_commision_estimasi->id_paket = $go->id_paket;
                        //         // $crete_commision_estimasi->amount = ($go->amount - $go->shipping_amount) * ($get_paket->paket->fee_commissions / 100);
                        //         // $crete_commision_estimasi->status = 'pending';
                        //         // $crete_commision_estimasi->type = 'belanja';
                        //         // $crete_commision_estimasi->save();

                        //         // $sum_all_order = Order::where([
                        //         //     ['user_id', $go->user_id],
                        //         //     ['id_paket', $go->id_paket],
                        //         //     ['status', '<>', OrderStatusEnum::CANCELED()],
                        //         //     ['status', '<>', OrderStatusEnum::RETURNED()]
                        //         // ])
                        //         //     ->whereHas('payment', function ($query) {
                        //         //         $query->where('status', PaymentStatusEnum::COMPLETED());
                        //         //     })
                        //         //     ->whereNotIn('id',[$go->id])
                        //         //     ->whereDate('created_at', '>=', $previousDate)
                        //         //     ->whereDate('created_at', '<=', $nextDate)
                        //         //     ->selectRaw('SUM(amount - shipping_amount) as total_amount')
                        //         //     ->first()
                        //         //     ->total_amount;

                        //         // if ($sum_all_order) {
                        //         //     if ((int)$sum_all_order < (int)$get_paket->paket->nominal) {
                        //         //         $sum_all_order += $go->amount - $go->shipping_amount;
                        //         //     }
                        //         // } else {
                        //         //     $sum_all_order = $go->amount - $go->shipping_amount;
                        //         // }

                        //         // if ((int)$sum_all_order >= (int)$get_paket->paket->nominal) {
                        //         //     $get_all_commisions_pending = CommisionsEstimasi::where([
                        //         //         ['id_paket', $go->id_paket],
                        //         //         ['status', 'pending']
                        //         //     ])
                        //         //         ->whereNotIn('id',[$crete_commision_estimasi->id])
                        //         //         ->whereDate('created_at', '>=', $previousDate)
                        //         //         ->whereDate('created_at', '<=', $nextDate)
                        //         //         ->get();


                        //         //     $commisions_fix = $crete_commision_estimasi->amount;
                        //         //     foreach ($get_all_commisions_pending as $gacp) {
                        //         //         $commisions_fix += $gacp->amount;
                        //         //         $gacp->status = 'completed';
                        //         //         $gacp->save();
                        //         //     }

                        //         //     if ($get_parent) {
                        //         //         $totalWithdrawal = MemberWithdrawal::where('customer_id', $get_parent->id)
                        //         //         ->selectRaw('SUM(amount) as total_withdrawal')
                        //         //         ->first()
                        //         //         ->total_withdrawal;

                        //         //         if (!$totalWithdrawal) {
                        //         //             $totalWithdrawal = 0;
                        //         //         }

                        //         //         $get_parent->commissions_shopping += $commisions_fix;
                        //         //         $get_parent->commissions = ($get_parent->commissions_referral + $get_parent->commissions_shopping) - $totalWithdrawal;
                        //         //         $get_parent->save();
                        //         //     }
                        //         // }
                        //         // return $sum_all_order;
                        //     }
                        // }
                    }
                }
            }
            do_action(ACTION_AFTER_UPDATE_PAYMENT, $request, $payment);
            DB::commit();
            return $response
                ->setPreviousUrl(route('payment.show', $payment->id))
                ->setMessage(trans('core/base::notices.update_success_message'));
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    private function getJNE($zip_code)
    {
        $jneData = Cache::remember('jne_data_' . $zip_code, 15, function () use ($zip_code) {
            return JneSupport::where(
                'zip_code',
                $zip_code
            )->first();
        });

        return $jneData;
    }

    private function getOrigin($province, $city)
    {
        $query = JneOrigin::where('origin_province', $province)->get();
        if (count($query) > 0) {
            foreach ($query as $qr) {
                if ($qr->origin_name === $city) {
                    return $query = $qr;
                }
            }
        } else {
            $query = JneOrigin::where('origin_name', $province)->first();
        }
        return $query;
    }

    private function generateAirWayBill($payment, $store, $customer, $order, $shipment)
    {
        $curl = curl_init();
        $store_jne = $this->getJNE($store->zip_code);
        $customer_jne = $this->getJNE($customer->zip_code);

        $start = microtime(true);
        $end = microtime(true);
        $executionTime = ($end - $start) * 1000;

        // return $store_jne;
        $origin = $this->getOrigin($store_jne->city_name, $store_jne->district_name);
        // $origin = $this->getOrigin('CILACAP','KESUGIHAN');

        // return $origin;
        $data = [
            'username' => 'TESTAPI',
            'api_key' => '25c898a9faea1a100859ecd9ef674548',
            'OLSHOP_BRANCH' => 'CGK000',
            'OLSHOP_CUST' => 'TESTAKUN',
            'OLSHOP_ORDERID' => $payment->charge_id,
            'OLSHOP_SHIPPER_NAME' => $store->name,
            'OLSHOP_SHIPPER_ADDR1' => $store->address,
            'OLSHOP_SHIPPER_ADDR2' => $store_jne->subdistrict_name,
            'OLSHOP_SHIPPER_ADDR3' => $store_jne->subdistrict_name,
            'OLSHOP_SHIPPER_CITY' => $store_jne->city_name,
            'OLSHOP_SHIPPER_REGION' => $store_jne->province_name,
            'OLSHOP_SHIPPER_ZIP' => $store->zip_code,
            'OLSHOP_SHIPPER_PHONE' => $store->phone,
            'OLSHOP_RECEIVER_NAME' => $customer->name,
            'OLSHOP_RECEIVER_ADDR1' => $customer->address,
            'OLSHOP_RECEIVER_ADDR2' => $customer_jne->subdistrict_name,
            'OLSHOP_RECEIVER_ADDR3' => $customer_jne->subdistrict_name,
            'OLSHOP_RECEIVER_CITY' => $customer_jne->city_name,
            'OLSHOP_RECEIVER_REGION' => $customer_jne->province_name,
            'OLSHOP_RECEIVER_ZIP' => $customer->zip_code,
            'OLSHOP_RECEIVER_PHONE' => $customer->phone,
            'OLSHOP_QTY' => $order->total_qty,
            'OLSHOP_WEIGHT' => $shipment->weight / 1000,
            'OLSHOP_GOODSDESC' => 'TEST',
            'OLSHOP_GOODSVALUE' => str_replace('.00', '', $order->amount),
            'OLSHOP_GOODSTYPE' => 2,
            'OLSHOP_INST' => 'TEST',
            'OLSHOP_INS_FLAG' => 'N',
            'OLSHOP_ORIG' => $origin->origin_code,
            'OLSHOP_DEST' => $customer_jne->tarif_code,
            'OLSHOP_SERVICE' => $order->shipping_service,
            'OLSHOP_COD_FLAG' => 'N',
            'OLSHOP_COD_AMOUNT' => 0
        ];

        // return $data;
        $queryString = http_build_query($data);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://apiv2.jne.co.id:10102/tracing/api/generatecnote',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "$queryString",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return (object)json_decode($response)->detail[0];
    }

    public function getRefundDetail($id, $refundId, BaseHttpResponse $response)
    {
        $data = [];
        $payment = $this->paymentRepository->findOrFail($id);

        $data = apply_filters(PAYMENT_FILTER_GET_REFUND_DETAIL, $data, $payment, $refundId);

        if (!Arr::get($data, 'error') && Arr::get($data, 'data', [])) {
            $metadata = $payment->metadata;
            $refunds = Arr::get($metadata, 'refunds', []);
            if ($refunds) {
                foreach ($refunds as $key => $refund) {
                    if (Arr::get($refund, '_refund_id') == $refundId) {
                        $refunds[$key] = array_merge($refunds[$key], (array) Arr::get($data, 'data'));
                    }
                }

                Arr::set($metadata, 'refunds', $refunds);
                $payment->metadata = $metadata;
                $payment->save();
            }
        }

        $view = Arr::get($data, 'view');

        if ($view) {
            $response->setData($view);
        }

        return $response
            ->setError((bool) Arr::get($data, 'error'))
            ->setMessage(Arr::get($data, 'message', ''));
    }
}
