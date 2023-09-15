<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\UpdateOrderRequest;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Marketplace\Http\Requests\UpdateShippingStatusRequest;
use Botble\Marketplace\Tables\OrderTable;
use Botble\Marketplace\Tables\RevenueAllTable;
use Botble\Marketplace\Tables\EtalaseTable;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Carbon\Carbon;
use EmailHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Botble\Ecommerce\Models\Shipment;
use InvoiceHelper;
use MarketplaceHelper;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use OrderHelper;

use Html;
use RvMedia;
use BaseHelper;
use Botble\Ecommerce\Models\JneOrigin;
use Botble\Ecommerce\Models\JneSupport;
use Botble\Ecommerce\Models\JntSupport;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Marketplace\Models\Store;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PDFHelper;
use Botble\Base\Supports\TwigCompiler;
use Botble\Base\Supports\TwigExtension;

class OrderController extends BaseController
{
    protected OrderInterface $orderRepository;

    protected OrderHistoryInterface $orderHistoryRepository;

    protected OrderAddressInterface $orderAddressRepository;

    protected PaymentInterface $paymentRepository;

    protected ProductInterface $productRepository;

    public function __construct(
        OrderInterface $orderRepository,
        OrderHistoryInterface $orderHistoryRepository,
        OrderAddressInterface $orderAddressRepository,
        PaymentInterface $paymentRepository,
        ProductInterface $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->paymentRepository = $paymentRepository;
        $this->productRepository = $productRepository;

        Assets::setConfig(config('plugins.marketplace.assets', []));
    }

    public function index(OrderTable $table)
    {
        page_title()->setTitle(__('Orders'));


        $orders = auth('customer')->user()->store->orders()->get();

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'), compact('orders'));
    }

    public function revenue(RevenueAllTable $table)
    {
        page_title()->setTitle(__('Revenue'));


        $orders = auth('customer')->user()->store->orders()->get();

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.base'), compact('orders'));
    }

    public function edit(int $id)
    {
        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/order.js',
            ])
            ->addScripts(['blockui', 'input-mask']);

        $order = $this->orderRepository
            ->getModel()
            ->where('id', $id)
            ->with(['products', 'user'])
            ->firstOrFail();

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        page_title()->setTitle(trans('plugins/ecommerce::order.edit_order', ['code' => $order->code]));

        $weight = $order->products_weight;

        $defaultStore = get_primary_store_locator();
        $shipment = Shipment::where('order_id', $order->id)->first();
        if ($shipment->shipment_id !== '') {
            if (strpos($shipment->shipping_company_name, 'J&T') !== false) {

                $shipment->tracking = $this->getTrackingJNT($shipment->shipment_id);
            }else{
                $awb = $shipment->shipment_id;
                $shipment->tracking =  $this->getTrackingJNE($awb);
                // ($this->getTracking($awb) && !$this->getTracking($awb)->status) ? $shipment->tracking =  $this->getTracking($awb) : null;
            }
        }
        $order->shipment = $shipment;
        // return $order;
        return MarketplaceHelper::view('dashboard.orders.edit', compact('order', 'weight', 'defaultStore'));
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

    private function getTrackingJNT($awb){
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
        CURLOPT_POSTFIELDS =>'{
            "awb": "'.$awb.'",
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

    public function update(int $id, UpdateOrderRequest $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->createOrUpdate($request->input(), ['id' => $id]);

        event(new UpdatedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));

        return $response
            ->setPreviousUrl(route('orders.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int $id, Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        try {
            $this->orderRepository->deleteBy(['id' => $id]);
            event(new DeletedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));

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
            $order = $this->orderRepository->findOrFail($id);

            if ($order->store_id != auth('customer')->user()->store->id) {
                abort(404);
            }

            $this->orderRepository->delete($order);
            event(new DeletedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function getGenerateInvoice(int $orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        return InvoiceHelper::downloadInvoice($order->invoice);
    }

    public function getGenerateResi(int $orderId)
    {
        $order = $this->orderRepository->getFirstBy(
            [
                'id' => $orderId,
            ],
            ['ec_orders.*'],
            ['address', 'products','store']
        );
        $shipment = Shipment::where('order_id', $order->id)->first();
        $order->shipment = $shipment;

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        // return $order;
        $path = platform_path('plugins/marketplace/resources/views/themes/dashboard/orders/cetak_resi.tpl');
        $templateHtml = BaseHelper::getFileData($path, false);

        $twigCompilerOrder = (new TwigCompiler());
        $templateHtml = $twigCompilerOrder->compile($templateHtml, $this->getDataForInvoiceTemplate($order));

        $pdf = Pdf::loadHTML((string)$templateHtml,'UTF-8');

        return $pdf
            ->setPaper('a6')
            ->setWarnings(false)
            ->setOption('tempDir', storage_path('app'))
            ->setOption('logOutputFile', storage_path('logs/pdf.log'))
            ->setOption('isRemoteEnabled', true)
            ->stream($order->shipment->shipment_id.'.pdf');
    }

    protected function getDataForInvoiceTemplate(Order $order)
    {
        $logo = get_ecommerce_setting('company_logo_for_invoicing') ?: (theme_option(
            'logo_in_invoices'
        ) ?: theme_option('logo'));


        $logoJnt = 'daco-5026733.png';
        $logoJne = 'daco-4702576.png';

        $logoShipper = '';

        if(strpos($order->shipment->shipping_company_name, 'J&T') !== false){
            $logoShipper = RvMedia::getRealPath($logoJnt);
        }else{
            $logoShipper = RvMedia::getRealPath($logoJne);
        }

        return [
            'logo' => $logo,
            'logo_full_path' => RvMedia::getRealPath($logo),
            'order' => $order,
            'logoShipper' => $logoShipper,
            'barcode' => 'data:image/png;base64,'. DNS1D::getBarcodePNG($order->shipment->shipment_id, 'C39+',2,2)
        ];
    }

    public function postConfirm(Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($request->input('order_id'));

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        $order->is_confirmed = 1;
        if ($order->status == OrderStatusEnum::PENDING) {
            $order->status = OrderStatusEnum::PROCESSING;
        }

        $this->orderRepository->createOrUpdate($order);

        $this->orderHistoryRepository->createOrUpdate([
            'action' => 'confirm_order',
            'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
            'order_id' => $order->id,
            'user_id' => 0,
        ]);

        $payment = $this->paymentRepository->getFirstBy(['order_id' => $order->id]);

        if ($payment) {
            $payment->user_id = 0;
            $payment->save();
        }

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('order_confirm')) {
            OrderHelper::setEmailVariables($order);
            $mailer->sendUsingTemplate(
                'order_confirm',
                $order->user->email ?: $order->address->email
            );
        }

        return $response->setMessage(trans('plugins/ecommerce::order.confirm_order_success'));
    }

    public function postResendOrderConfirmationEmail(int $id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        $result = OrderHelper::sendOrderConfirmationEmail($order);

        if (!$result) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.error_when_sending_email'));
        }

        return $response->setMessage(trans('plugins/ecommerce::order.sent_confirmation_email_success'));
    }

    public function postUpdateShippingAddress(int $id, AddressRequest $request, BaseHttpResponse $response)
    {
        $address = $this->orderAddressRepository->createOrUpdate($request->input(), compact('id'));

        if (!$address) {
            abort(404);
        }

        if ($address->order->status == OrderStatusEnum::CANCELED) {
            abort(401);
        }

        return $response
            ->setData([
                'line' => view('plugins/ecommerce::orders.shipping-address.line', compact('address'))->render(),
                'detail' => view('plugins/ecommerce::orders.shipping-address.detail', compact('address'))->render(),
            ])
            ->setMessage(trans('plugins/ecommerce::order.update_shipping_address_success'));
    }

    public function postCancelOrder(int $id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->store_id != auth('customer')->user()->store->id) {
            abort(404);
        }

        if (!$order->canBeCanceledByAdmin()) {
            abort(403);
        }

        OrderHelper::cancelOrder($order);

        $this->orderHistoryRepository->createOrUpdate([
            'action' => 'cancel_order',
            'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
            'order_id' => $order->id,
            'user_id' => 0,
        ]);

        return $response->setMessage(trans('plugins/ecommerce::order.customer.messages.cancel_success'));
    }

    public function updateShippingStatus(
        int $id,
        UpdateShippingStatusRequest $request,
        BaseHttpResponse $response,
        ShipmentInterface $shipmentRepository,
        ShipmentHistoryInterface $shipmentHistoryRepository
    ) {
        $shipment = $shipmentRepository->findOrFail($id);

        $status = $request->input('status');

        $getStore = Store::find($shipment->store_id);

        if($getStore && $getStore->kecamatan === null && $getStore->kelurahan === null && $getStore->city === null && $getStore->address === ""){
            return $response->setError()->setMessage('Silahkan lengkapi alamat anda terlebih dahulu pada bagian setting.');
        }

        $shipmentRepository->createOrUpdate(['status' => $status], compact('id'));

        $shipmentHistoryRepository->createOrUpdate([
            'action' => 'update_status',
            'description' => trans('plugins/ecommerce::shipping.changed_shipping_status', [
                'status' => ShippingStatusEnum::getLabel($status),
            ]),
            'shipment_id' => $id,
            'order_id' => $shipment->order_id,
            'user_id' => Auth::id() ?? 0,
        ]);

        switch ($status) {
            case ShippingStatusEnum::DELIVERED:
                $this->orderHistoryRepository->createOrUpdate([
                    'action' => 'delivery',
                    'description' => trans('plugins/ecommerce::shipping.order_confirmed_by'),
                    'order_id' => $shipment->order_id,
                    'user_id' => Auth::id(),
                ]);
                // $shipment->date_shipped = Carbon::now();
                // $shipment->save();

                // OrderHelper::shippingStatusDelivered($shipment, $request);
                $this->updateShipment($shipment, $response);
                break;

            case ShippingStatusEnum::CANCELED:
                $this->orderHistoryRepository->createOrUpdate([
                    'action' => 'cancel_shipment',
                    'description' => trans('plugins/ecommerce::shipping.shipping_canceled_by'),
                    'order_id' => $shipment->order_id,
                    'user_id' => Auth::id(),
                ]);

                break;
            case ShippingStatusEnum::ARRANGE_SHIPMENT:
                $this->orderHistoryRepository->createOrUpdate([
                    'action' => 'arrange_shipment',
                    'description' => trans('plugins/ecommerce::shipping.arrange_shipment'),
                    'order_id' => $shipment->order_id,
                    'user_id' => Auth::id(),
                ]);
                break;
        }

        return $response->setMessage(trans('plugins/ecommerce::shipping.update_shipping_status_success'));
    }

    protected function updateShipment($shipment, $response)
    {
        $get_order = Order::where('id', $shipment->order_id)->first();
        $store = Store::where('id', $get_order->store_id)->first();
        $order_details = OrderProduct::where('order_id', $get_order->id)->get();
        $customer = OrderAddress::where('order_id', $get_order->id)->first();

        $total_qty = 0;
        foreach ($order_details as $dt) {
            $total_qty += $dt->qty;
        }
        $get_order->total_qty = $total_qty;

        $shipment_id = '';
        if (strpos($shipment->shipping_company_name, 'J&T') !== false) {
            $shipment_id = $this->generateAirWayBillJNT($store,$get_order,$customer,$shipment);
        }else{
            // disable if in production
            $shipment_id = '5403212200022724';

            // enbale this if in production
            // $shipmentJNE = $this->generateAirWayBillJNE($store, $customer, $get_order, $shipment);
            // if ($shipmentJNE->status === 'sukses') {
            //     $shipment_id = $shipmentJNE->cnote_no;
            // }else{
            //     return $response
            //         ->setError()
            //         ->setMessage('JNE error : '.$shipmentJNE->reason);
            // }
        }

        $shipment->shipment_id = $shipment_id;
        $shipment->save();
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

    private function generateAirWayBillJNT($store,$order,$customer,$shipment)
    {
        Carbon::now()->setTimezone('Asia/Jakarta');
        $curl = curl_init();
        $key = 'AKe62df84bJ3d8e4b1hea2R45j11klsb';

        $storeCityCode = JntSupport::where([
            ['kode_pos',$store->zip_code]
        ])->first();

        $customerCityCode = JntSupport::where([
            ['kode_pos',$customer->zip_code]
        ])->first();

        $data = array(
            'username'=>'SOBERMART',
            'api_key'=>'DEKY3W',
            'orderid'=>'SBR-'.rand(100, 9999).'-'.$order->id,
            'shipper_name'=>$store->name,
            'shipper_contact'=>$store->name,
            'shipper_phone'=> $store->phone,
            'shipper_addr'=>$store->address,
            'origin_code'=>$storeCityCode->kode_kota,
            'receiver_name'=>$customer->name,
            'receiver_phone'=>$customer->phone,
            'receiver_addr'=>$customer->address,
            'receiver_zip'=>$customer->zip_code,
            'destination_code'=>$customerCityCode->kode_kota,
            'receiver_area'=>$customerCityCode->kode_jnt,
            'qty'=>$order->total_qty,
            'weight'=> (($shipment->weight / 1000) < 1) ? 1 : $shipment->weight / 1000,
            'goodsdesc'=>'TESTING!!',
            'servicetype'=>'1',
            'insurance'=>'0',
            'orderdate'=>Carbon::now()->format('Y-m-d H:i:s'),
            'item_name'=>'belanja',
            'cod'=>'',
            'sendstarttime'=>Carbon::now()->format('Y-m-d H:i:s'),
            'sendendtime'=>Carbon::now()->addHours(12)->format('Y-m-d H:i:s'),
            'expresstype'=>'1',
            'goodsvalue'=>str_replace('.00', '', $order->amount),
        );
        $data_param = json_encode(array('detail'=>array($data)));
        $signature = base64_encode(md5($data_param . $key));

        $payload = [
            "data_param" => $data_param,
            'data_sign' => $signature
        ];
        $queryString = http_build_query($payload);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.jet.co.id/jts-idn-ecommerce-api/api/order/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $queryString,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $responses = json_decode($response);

        $result = '';
        if ($responses->success) {
            $result = $responses->detail[0]->awb_no;
        }

        return $result;
    }

    private function generateAirWayBillJNE($store, $customer, $order, $shipment)
    {
        $curl = curl_init();
        $store_jne = $this->getJNE($store->zip_code);
        $customer_jne = $this->getJNE($customer->zip_code);

        $start = microtime(true);
        $end = microtime(true);
        $executionTime = ($end - $start) * 1000;

        $data = [
            'username' => 'TESTAPI',
            'api_key' => '25c898a9faea1a100859ecd9ef674548',
            'OLSHOP_BRANCH' => 'CGK000',
            // enable if in production
            // 'OLSHOP_BRANCH' => $store->branch_shipment,
            'OLSHOP_CUST' => 'TESTAKUN',
            'OLSHOP_ORDERID' => $order->id,
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
            'OLSHOP_WEIGHT' => (($shipment->weight / 1000) < 1) ? 1 : $shipment->weight / 1000,
            'OLSHOP_GOODSDESC' => 'TEST',
            'OLSHOP_GOODSVALUE' => str_replace('.00', '', $order->amount),
            'OLSHOP_GOODSTYPE' => 2,
            'OLSHOP_INST' => 'TEST',
            'OLSHOP_INS_FLAG' => 'N',
            'OLSHOP_ORIG' => $store->origin_shipment,
            'OLSHOP_DEST' => $customer_jne->tarif_code,
            'OLSHOP_SERVICE' => $order->shipping_service,
            'OLSHOP_COD_FLAG' => 'N',
            'OLSHOP_COD_AMOUNT' => 0
        ];

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
}
