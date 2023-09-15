<?php

namespace Theme\Farmart\Http\Controllers;

use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\JneSupport;
use Botble\Ecommerce\Models\JneOrigin;
use Botble\Payment\Models\Midtrans;
use Botble\Payment\Models\Payment;
use Botble\Ecommerce\Models\MemberPaket;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Shipment;
use Botble\Marketplace\Models\Store;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;

use Botble\Ecommerce\Models\Cart as CartModel;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\OptionValue;
use Botble\Base\Enums\BaseStatusEnum;

class PaymentController extends PublicController
{
    public function getIndex(){
        return 'aaa';
    }

    public function detailsPayment(
        Request $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    ) {
        $customer_request = explode(';',base64_decode($request->customer));
        $selected_cart = explode(',',base64_decode($customer_request[0]));
        $customer_id = base64_decode($customer_request[1]);

        $code_uniques = (isset($request->code_unique) ? $request->code_unique : 0);
        $voucher_applied = (isset($request->voucher_applied) ? $request->voucher_applied : 0);

        $address = (object)[];
        if ($request->address !== 'new') {
            $address = Address::where('id', $request->address)->first();
        } else {
            $post_address = (object)$request->new_address;
            $address = new Address;
            $address->name = $post_address->name;
            $address->email = $post_address->email;
            $address->phone = $post_address->phone;
            $address->country = $post_address->country;
            $address->city = $post_address->city;
            $address->state = $post_address->state;
            $address->address = $post_address->address;
            $address->zip_code = $post_address->zip_code;
            $address->customer_id = $request->customer;
        }

        $shimpents = $request->shimpent;;
        $carts = $this->getCart($selected_cart,$customer_id);

        $total_price = 0;
        $total_item = 0;
        $total_pengiriman = 0;

        foreach ($carts as $cart) {
            $shippingAmount = 0;
            $total_price += $cart['total_price'];
            $total_item += $cart['total_item'];
            $store = $cart['store'];
            $shipment = $this->filterShipmentsByStoreId($shimpents, $store->id);

            $shippingAmount += ($shipment['free_shipping'] !== '') ? $shipment['courier_price'] - $shipment['free_shipping'] : $shipment['courier_price'];
            $total_pengiriman += $shippingAmount;

            $cart['store']->shipment = (object)$shipment;
        }

        return $response->setData([
            'cart' => $carts,
            'address' => $address,
            'total_price' => ($total_price - $voucher_applied)+$code_uniques,
            'total_pengiriman' => $total_pengiriman,
            'total_item' => $total_item+1,
        ]);
    }

    function filterShipmentsByStoreId($shipments, $storeId)
    {
        $filteredShipments = (object)[];

        foreach ($shipments as $shipment) {
            if (isset($shipment['store_id']) && $shipment['store_id'] == $storeId) {
                $shipment['courier_details'] = json_decode(base64_decode($shipment['courier_details']));
                $filteredShipments = $shipment;
            }
        }

        return $filteredShipments;
    }

    private function getCart($selected_cart,$customer)
    {

        $cartsDB = CartModel::where('customer_id', $customer)
            ->whereIn('id', $selected_cart)
            ->get();

        $store = [];
        $carts = [];
        foreach ($cartsDB as $cart) {
            $total_price = 0;
            $total_item = 0;
            $total_weight = 0;

            $product = Product::where([
                ['id', $cart->product_id],
                ['status', BaseStatusEnum::PUBLISHED]
            ])->first();

            if ($product) {
                $product->variant_config = $product->is_variation ? ProductVariation::where(
                    'product_id',
                    $cart->product_id
                )->with('productAttributes.productAttributeSet')->first() : '';

                $price_option_ = 0;
                if ($cart->options !== '') {
                    $options = explode(';', $cart->options);
                    $objects = [];
                    foreach ($options as $option) {
                        $object = json_decode($option);
                        $objects[] = $object;
                    }
                    $options = array();
                    foreach ($objects as $obj) {
                        $option = OptionValue::where([[
                            'option_id',
                            $obj->option_id
                        ], ['id', $obj->id]])->with('option')->first();

                        if ($option->affect_type == 1) {
                            $price_option_ += ($product->front_sale_price_with_taxes * $option->affect_price) / 100;
                        } else {
                            $price_option_ += $option->affect_price;
                        }

                        $options[] = $option;
                    }
                    $cart->option = $options;
                } else {
                    $cart->option = [];
                }

                $store = Store::where('id', $product->original_product->store_id)->first();
                if ($store) {
                    $product->weight = ($product->weight / 1000) * $cart->qty;
                    $product->sale_price_front = $product->front_sale_price + $price_option_;
                    $total_item += $cart->qty;
                    $total_weight += ($product->weight < 0) ? 1 : $product->weight;
                    $total_price += (($product->front_sale_price_with_taxes + $price_option_) *
                        $cart->qty);

                    $product->cart = $cart;
                    if (!isset($carts[$store->id])) {
                        $carts[$store->id] = [
                            'store' => $store,
                            'total_item' => 0,
                            'total_price' => 0,
                            'total_weight' => 0,
                            'products' => []
                        ];
                    }

                    $carts[$store->id]['products'][] = $product;
                    $carts[$store->id]['total_item'] += $total_item;
                    $carts[$store->id]['total_price'] += $total_price;
                    $carts[$store->id]['total_weight'] += $total_weight;
                }
            }
        }
        return collect($carts)->values();
    }

    public function postStore(Request $request) {
        (object)$request;

        if ($request->status_code === '200') {
            // $GET_PAYMENT_DATA = DB::select("SELECT * FROM payments WHERE charge_id = '$request->order_id' AND status = 'pending'");
            $GET_PAYMENT_DATA = Payment::where([['charge_id',$request->order_id]])->get();
            if(count($GET_PAYMENT_DATA) > 0) {
                foreach($GET_PAYMENT_DATA as $GPD){
                    $GET_PAYMENT_ID = Payment::where('id',$GPD->id)->first();
                    $GET_PAYMENT_ID->status = 'completed';
                    $GET_PAYMENT_ID->save();
                    if($GET_PAYMENT_ID->type_status === 'paket'){
                        $GET_CUSTOMER_PAKET = MemberPaket::where('id',$GET_PAYMENT_ID->order_id)->first();
                        $GET_CUSTOMER_PAKET->expire_date = Carbon::now()->addYear();
                        $GET_CUSTOMER_PAKET->save();
                    }
                    // return $GET_PAYMENT_ID;
                }
            }else{
                return response()->json(['error' => 'Internal Server Error'], 500);
            }
        }else if ($request->status_code === '201'){
            $MIDTRANS = new Midtrans;
            $MIDTRANS->order_id = $request->order_id;
            $MIDTRANS->metadata = json_encode($request->input());
            $MIDTRANS->save();

            // return response()->json(['error' => 'Internal Server Error'], 500);
            // return $request->va_numbers[0]['bank'];
        }
    }

    public function getAccPayment(Request $request){
        $midtrans =  Midtrans::first();
        $midtrans->metadata = json_decode($midtrans->metadata);
        $midtrans->metadata->data = json_decode($midtrans->metadata->data);

        return $midtrans;
    }


    public function postAccPayment(Request $request){
        // $data = $request->input('data');
        // return $data;

        $data = (object)json_decode($request->input('data'),true);
        DB::beginTransaction();
        try {
            if($data->status === 'SUCCESSFUL'){
                $payment = Payment::where('charge_id',$data->bill_link_id)->first();
                if($payment) {
                    $payment->status = 'completed';
                    // $payment->bank = $data->sender_bank;
                    if($payment->save()){
                        if($payment->type_status === 'paket'){
                            $get_member_paket = MemberPaket::where('id',$payment->order_id)->first();
                            if($get_member_paket){
                                $get_member_paket->expire_date = Carbon::now();

                                $get_member_paket->save();
                            }
                        }else{
                            $get_order = Order::where('payment_id',$payment->id)->get();
                            foreach($get_order as $order){
                                $store = Store::where('id',$order->store_id)->first();
                                $customer = OrderAddress::where('order_id',$order->id)->first();
                                $order_details = OrderProduct::where('order_id',$order->id)->get();
                                $shipment = Shipment::where('order_id',$order->id)->first();

                                $total_qty = 0;
                                $total_qty = 0;
                                foreach($order_details as $dt){
                                    $total_qty += $dt->qty;
                                }
                                $order->total_qty = $total_qty;
                                $shipmentJNE = $this->generateAirWayBill($payment,$store,$customer,$order,$shipment);
                                // return $shipmentJNE;
                                if($shipmentJNE->status === 'sukses'){
                                    $shipment->shipment_id = $shipmentJNE->cnote_no;
                                    $shipment->save();
                                }
                            }
                        }
                        DB::commit();
                        return 'success';
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    private function getJNE($zip_code){
        $jneData = Cache::remember('jne_data_'.$zip_code, 15, function () use ($zip_code) {
            return JneSupport::where('zip_code',
            $zip_code)->first();
        });

        return $jneData;
    }

    private function getOrigin($province,$city){
        $query = JneOrigin::where('origin_province',$province)->get();
        if(count($query) > 0){
            foreach($query as $qr){
                if($qr->origin_name === $city){
                    return $query = $qr;
                }
            }
        }else{
            $query = JneOrigin::where('origin_name',$province)->first();
        }
        return $query;
    }

    private function generateAirWayBill($payment,$store,$customer,$order,$shipment){
        $curl = curl_init();
        $store_jne = $this->getJNE($store->zip_code);
        $customer_jne = $this->getJNE($customer->zip_code);

        $start = microtime(true);
        $end = microtime(true);
        $executionTime = ($end - $start) * 1000;

        $origin = $this->getOrigin($store_jne->city_name,$store_jne->district_name);
        // $origin = $this->getOrigin('CILACAP','KESUGIHAN');

        // return $customer_jne;
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
            'OLSHOP_WEIGHT' => $shipment->weight/1000,
            'OLSHOP_GOODSDESC' => 'TEST',
            'OLSHOP_GOODSVALUE' => str_replace('.00','',$order->amount),
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
}
