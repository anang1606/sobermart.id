<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderAddressTypeEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Events\OrderPlacedEvent;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CheckoutRequest;
use Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Services\Footprints\FootprinterInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Carbon\Carbon;
use Cart;
use EcommerceHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OptimizerHelper;
use OrderHelper;
use Theme;
use Validator;
use EmailHandler;
use Botble\Ecommerce\Models\Cart as CartModel;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\OptionValue;
use Botble\Marketplace\Models\Store;
use Botble\Base\Enums\BaseStatusEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Shipment;
use Botble\Payment\Models\Payment;
use Botble\Ecommerce\Models\BankList;
use Botble\Ecommerce\Models\DiscountClaim;
use Botble\Ecommerce\Models\MemberPaket;

use Illuminate\Support\Facades\Cache;
use Botble\Ecommerce\Models\JneSupport;
use Botble\Ecommerce\Models\JntSupport;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Eloquent;

class PublicCheckoutController
{
    protected TaxInterface $taxRepository;

    protected OrderInterface $orderRepository;

    protected OrderProductInterface $orderProductRepository;

    protected OrderAddressInterface $orderAddressRepository;

    protected AddressInterface $addressRepository;

    protected CustomerInterface $customerRepository;

    protected ShippingInterface $shippingRepository;

    protected OrderHistoryInterface $orderHistoryRepository;

    protected ProductInterface $productRepository;

    protected DiscountInterface $discountRepository;

    public function __construct(
        TaxInterface $taxRepository,
        OrderInterface $orderRepository,
        OrderProductInterface $orderProductRepository,
        OrderAddressInterface $orderAddressRepository,
        AddressInterface $addressRepository,
        CustomerInterface $customerRepository,
        ShippingInterface $shippingRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ProductInterface $productRepository,
        DiscountInterface $discountRepository,
    ) {
        $this->taxRepository = $taxRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->shippingRepository = $shippingRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->productRepository = $productRepository;
        $this->discountRepository = $discountRepository;
        OptimizerHelper::disable();
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

    private function getCouponData(string $couponCode, array $sessionData): Model|Eloquent|Builder|null
    {
        $couponCode = trim($couponCode);

        return $this->discountRepository
            ->getModel()
            ->where('code', $couponCode)
            ->where('type', 'coupon')
            ->where('start_date', '<=', Carbon::now())
            ->where(function ($query) use ($sessionData) {
                /**
                 * @var Builder $query
                 */
                $query
                    ->where(function ($sub) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->whereIn('type_option', ['amount', 'percentage'])
                            ->where(function ($subSub) {
                                /**
                                 * @var Builder $subSub
                                 */
                                return $subSub
                                    ->whereNull('end_date')
                                    ->orWhere('end_date', '>=', Carbon::now());
                            });
                    })
                    ->orWhere(function ($sub) use ($sessionData) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->where('type_option', 'shipping')
                            ->where('value', '>=', Arr::get($sessionData, 'shipping_amount', 0))
                            ->where(function ($subSub) use ($sessionData) {
                                /**
                                 * @var Builder $subSub
                                 */
                                return $subSub
                                    ->whereNull('target')
                                    ->orWhere('target', 'all-orders');
                            });
                    })
                    ->orWhere(function ($sub) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->where('type_option', 'same-price')
                            ->whereIn('target', ['group-products', 'specific-product', 'product-variant']);
                    });
            })
            ->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query
                    ->whereNull('quantity')
                    ->orWhereRaw('quantity > total_used');
            })
            ->first();
    }

    private function applyVoucher(
        string $coupon,
        array $cart_selected = [],
        array $sessionData = [],
        array $cartData = [],
        ?string $prefix = ''
    ){
        if (!$sessionData) {
            $sessionData = array(
                'shipping_amount' => 0
            );
        }

        $result = false;

        $couponCode = trim($coupon);
        $discount = $this->getCouponData($couponCode, $sessionData);

        if ($discount->target === 'customer') {
            $discountCustomers = $discount->customers()->pluck('customer_id')->all();
        }

        $cartItems = [];
        $rawTotal = 0;
        $countCart = 0;
        foreach ($cart_selected as $cart) {
            $cartItemsDB = CartModel::where([['id', $cart], ['customer_id', auth('customer')->id()]])->get();
            $countCart += $cartItemsDB->count();
            foreach ($cartItemsDB as $cartItem) {
                $product = Product::where([['id', $cartItem->product_id], ['status', BaseStatusEnum::PUBLISHED]])->first();
                if ($product) {
                    $cartItems[] = $product;
                    $option_price = 0;
                    if ($cartItem->options !== '') {
                        $options = explode(';', $cartItem->options);
                        $objects = [];
                        foreach ($options as $option) {
                            $object = json_decode($option);
                            $objects[] = $object;
                        }

                        foreach ($objects as $obj) {
                            $option = OptionValue::where([[
                                'option_id',
                                $obj->option_id
                            ], ['id', $obj->id]])->with('option')->first();

                            if ($option->affect_type == 1) {
                                $option_price += ($product->front_sale_price_with_taxes * $option->affect_price) / 100;
                            } else {
                                $option_price += $option->affect_price;
                            }
                        }
                    }

                    $rawTotal += ($product->front_sale_price_with_taxes + $option_price) * $cartItem->qty;
                }
            }
        }

        $couponDiscountAmount = 0;
        $isFreeShipping = false;
        $discountTypeOption = null;

        if ($discount->type_option == 'shipping') {
            $result = true;
        }elseif ($discount->type_option === 'amount' && $discount->discount_on === 'per-order') {
            $result = true;
        }else {
            $discountTypeOption = $discount->type_option;
            switch ($discount->type_option) {
                case 'amount':
                    switch ($discount->target) {
                        case 'amount-minimum-order':
                            if ($discount->min_order_price <= $rawTotal) {
                                $result = true;
                            } else {
                                $result = false;
                            }
                            break;
                        case 'all-orders':
                            $result = true;
                            break;
                        case 'specific-product':
                        case 'product-variant':
                            foreach ($cartItems as $item) {
                                if (in_array($item->original_product->id, $discount->products()->pluck('product_id')->all())) {
                                    $result = true;
                                }else{
                                    $result = false;
                                }
                            }
                            break;
                        default:
                            if ($countCart >= $discount->product_quantity) {
                                $result = true;
                            }
                            break;
                    }
                    break;
                case 'percentage':
                    switch ($discount->target) {
                        case 'amount-minimum-order':
                            if ($discount->min_order_price <= $rawTotal) {
                                $result = true;
                            }
                            break;
                        case 'all-orders':
                            $result = true;
                            break;
                        case 'specific-product':
                        case 'product-variant':
                            foreach ($cartItems as $item) {
                                if (in_array($item->original_product->id, $discount->products()->pluck('product_id')->all())) {
                                    $result = true;
                                }else{
                                    $result = false;
                                }
                            }

                            break;
                        default:
                            if ($countCart >= $discount->product_quantity) {
                                $result = true;
                            }
                            break;
                    }
                    break;
                case 'same-price':
                    foreach ($cartItems as $item) {
                        if (
                            in_array($discount->target, ['specific-product', 'product-variant']) &&
                            in_array($item->original_product->id, $discount->products()->pluck('product_id')->all())
                        ) {
                            $result = true;
                        } elseif ($product = $this->productRepository->findById($item->original_product->id)) {
                            $productCollections = $product
                                ->productCollections()
                                ->pluck('ec_product_collections.id')
                                ->all();

                            $discountProductCollections = $discount
                                ->productCollections()
                                ->pluck('ec_product_collections.id')
                                ->all();

                            if (!empty(array_intersect($productCollections, $discountProductCollections))) {
                                $result = true;
                            }
                        }
                    }
            }
        }

        return $result;
    }

    public function setVoucher(
        Request $request,
        HandleApplyCouponService $applyCouponService,
    ){
        $vouchers = [];
        foreach ($request->voucher as $value) {
            $value = explode(',',$value);

            $vouchers[] = array(
                'prefix' => $value[1],
                'code' => $value[0],
            );
        }

        $checkoutCart = explode(',', session()->get('checkout_selected_cart'));
        $selecteds = [];
        foreach ($checkoutCart as $select) {
            if ($select != '') {
                $selecteds[] = $select;
            }
        }

        foreach ($vouchers as $voucher) {
            $getVoucher = DiscountClaim::where([
                ['customer_id',auth('customer')->id()],
                ['id',$voucher['code']]
            ])
            ->first();

            if($getVoucher){
                $applyCouponService->execute($getVoucher->code_id,$selecteds);
                // $applyCouponService->execute($getVoucher->code_id,$selecteds,[],[],$voucher['prefix']);
            }
        }
        session()->put('voucher_applied',$vouchers);
        return redirect()->back();
    }

    public function getVoucherPrice(
        Request $request,
        BaseHttpResponse $response,
    ){
        $vouchersClaims = DiscountClaim::where('customer_id',auth('customer')->id())
        ->with('voucher')
        ->get();

        $checkoutCart = explode(',', session()->get('checkout_selected_cart'));
        $carts = $this->getCart($checkoutCart);
        $voucherSober = [];
        $collectVoucherDiscount = [];
        $collectVoucherShipping = [];
        $store = [];

        $total_item = 0;
        $total_price = 0;
        foreach ($carts as $cart) {
            $total_item += $cart['total_item'];
            $total_price += $cart['total_price'];

            $store[] = $cart['store']->id;
        }

        $appliedVoucher = [];
        if(session()->has('voucher_applied')){
            foreach (session()->get('voucher_applied') as $value) {
                $appliedVoucher[] = $value['code'];
            }
        }
        $selecteds = [];
        foreach ($checkoutCart as $select) {
            if ($select != '') {
                $selecteds[] = $select;
            }
        }
        $voucherStore = [];
        foreach ($vouchersClaims as $vouchersClaims) {
            $voucher = $vouchersClaims->voucher;
            if (!$voucher->isExpired()) {
                if ($voucher->store_id === null) {
                    if ($voucher->type_option === "shipping") {
                        $collectVoucherShipping[] = $voucher;
                    }else{
                        $collectVoucherDiscount[] = $voucher;
                    }
                }else{
                    if(in_array($voucher->store_id,$store)){
                        $getStore = Store::where('id',$voucher->store_id)->with('slugable')->first();
                        if($getStore){
                            $voucherStore['title'] = $getStore->name;
                            $voucherStore['slug'] = $getStore->slugable->key;
                            $voucherStore['data'][] = $voucher;
                        }
                    }
                }
                $voucher->id = $vouchersClaims->id;
                $voucher->apply = $this->applyVoucher($voucher->code,$selecteds);

                $voucher->is_seledted = in_array($voucher->id,$appliedVoucher);
            }
        }
        if(count($vouchersClaims) > 0){
            if($voucherStore){
                $voucherSober = array(
                    $voucherStore,
                    array(
                        'title' => 'Voucher Gratis Ongkir',
                        'slug' => 'voucher_gratis_ongkir',
                        'data' => $collectVoucherShipping
                    ),
                    array(
                        'title' => 'Voucher Diskon',
                        'slug' => 'voucher_diskon',
                        'data' => $collectVoucherDiscount
                    ),
                );
            }else{
                $voucherSober = array(
                    array(
                        'title' => 'Voucher Gratis Ongkir',
                        'slug' => 'voucher_gratis_ongkir',
                        'data' => $collectVoucherShipping
                    ),
                    array(
                        'title' => 'Voucher Diskon',
                        'slug' => 'voucher_diskon',
                        'data' => $collectVoucherDiscount
                    ),
                );
            }
        }


        // if($voucherStore){
        //     array_push($voucherSobers,$voucherStore);
        // }

        // $voucherSober = (object)$voucherSober;
        // return $voucherStore;
        return $response->setData(view('plugins/ecommerce::themes.customers.voucher.includes.sober-checkout',compact('voucherSober'))->render());
    }

    public function resetVoucher(){
        session()->forget('is_free_shipping');
        session()->forget('shipping_amount');
        session()->forget('applied_coupon_code');
        session()->forget('coupon_discount_amount');
        session()->forget('voucher_applied');

        return redirect()->back();
    }

    public function postCheckoutData(
        Request $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    ) {
        DB::beginTransaction();
        try {
            if (session()->has('checkout_selected_cart') && session()->get('checkout_selected_cart') === '') {
                // return redirect('public.checkout.success', session()->get('token_checkout_success'));
                // return $response->setNextUrl(route('public.checkout.success',session()->get('token_checkout_success')));
            } else {
                if (!EcommerceHelper::isCartEnabled()) {
                    abort(404);
                }
                if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
                    return $response->setNextUrl(route('customer.login'));
                }
                $address = (object)[];
                if ($request->address !== 'new') {
                    $address = Address::where('id', $request->address)->first();
                } else {
                    $validator = Validator::make($request->all(), [
                        'new_address.address' => 'required|string',
                        'new_address.city' => 'required|string',
                        'new_address.email' => 'required|email',
                        'new_address.name' => 'required|string',
                        'new_address.phone' => 'required|string',
                        'new_address.state' => 'required|string',
                        'new_address.zip_code' => 'required|int',
                    ]);

                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        foreach ($errors as &$error) {
                            $error = str_replace('new address.', '', $error);
                        }
                        return $response->setError(true)->setData($errors);
                    }

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
                    $address->customer_id = auth('customer')->id();
                    $address->save();
                }
                $shimpents = $request->shimpent;
                $selected_cart = explode(',', session()->get('checkout_selected_cart'));
                $carts = $this->getCart($selected_cart);

                if (count($selected_cart) < 0) {
                    return $response
                        ->setError()
                        ->setMessage(__('No products in cart'));
                }
                $productsCollection = collect();
                $total_item = 0;
                $total_price = 0;
                foreach ($carts as $cart) {
                    $store = $cart['store'];
                    $products = $cart['products'];
                    $total_item += $cart['total_item'];
                    $total_price += $cart['total_price'];

                    $productsCollection = $productsCollection->concat($products);
                }
                $digitalProducts = EcommerceHelper::countDigitalProducts($productsCollection);

                if ($digitalProducts && !auth('customer')->check()) {
                    return $response
                        ->setError()
                        ->setNextUrl(route('customer.login'))
                        ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
                }
                if (EcommerceHelper::getMinimumOrderAmount() > $total_item) {
                    return $response
                        ->setError()
                        ->setMessage(__('Minimum order amount is :amount, you need to buy more :more to place an order!', [
                            'amount' => format_price(EcommerceHelper::getMinimumOrderAmount()),
                            'more' => format_price(EcommerceHelper::getMinimumOrderAmount() - $total_item),
                        ]));
                }
                $token = bin2hex(random_bytes(75 / 2));
                $sessionData = [];
                $sessionData[] = $this->processCheckoutData($sessionData, $request);
                $code_unique = session()->get('code_unique');
                $total_payment = $code_unique;
                $total_pengiriman = 0;
                $total_diskon = 0;
                $id_cart = [];
                $id_order = [];
                $id_paket = '';
                $get_member = MemberPaket::where([['user_id', auth('customer')->id()], ['is_active', '1']])->first();
                if ($get_member) {
                    $id_paket = $get_member->id;
                }

                foreach ($carts as $cart) {
                    $shippingAmount = 0;
                    $total_payment += $cart['total_price'];
                    $products = $cart['products'];
                    $store = $cart['store'];
                    $shimpent = $this->filterShipmentsByStoreId($shimpents, $store->id);

                    $shippingAmount += ($shimpent['free_shipping'] !== '') ? $shimpent['courier_price'] - $shimpent['free_shipping'] : $shimpent['courier_price'];
                    $total_pengiriman += $shippingAmount;
                    foreach ($products as $product) {
                        if ($product->isOutOfStock()) {
                            return $response
                                ->setError()
                                ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name]));
                        }
                        $store->discount_amount = session()->get($product->cart->id . 'coupon_discount_amount');
                        $total_diskon += session()->get($product->cart->id . 'coupon_discount_amount');
                    }

                    $create_order = new Order;
                    $create_order->user_id = auth('customer')->id();
                    $create_order->shipping_service = $shimpent['courier_details']->service_display;
                    $create_order->status = OrderStatusEnum::PENDING;
                    $create_order->code = uniqid(date('YmdHis') . substr(microtime(), 2, 6));
                    $create_order->sub_total = $cart['total_price'];
                    $create_order->discount_amount = ($store->discount_amount) ? $store->discount_amount : 0;
                    $create_order->amount = ($cart['total_price'] - $create_order->discount_amount) + $shippingAmount;
                    $create_order->coupon_code = (session()->has('applied_coupon_code')) ?
                        session()->get('applied_coupon_code') : null;
                    $create_order->shipping_amount = $shippingAmount;
                    $create_order->token = $token;
                    $create_order->tax_amount = 0;
                    $create_order->id_paket = $id_paket;
                    $create_order->store_id = $store->id;

                    $total_weight = 0;
                    if ($create_order->save()) {
                        $id_order[] = $create_order->id;
                        foreach ($products as $product) {
                            $optionCartValue = [];
                            $optionInfo = [];
                            $result_option = [];
                            $total_weight += $product->weight;
                            $id_cart[] = $product->cart->id;

                            if (count($product->cart->option) > 0) {
                                foreach ($product->cart->option as $option) {
                                    $product_att['option_value'] = $option->option_value;
                                    $product_att['affect_price'] = $option->affect_price;
                                    $product_att['affect_type'] = $option->affect_type;

                                    $optionCartValue['optionCartValue'][$option->option_id][] = $product_att;
                                    $optionInfo['optionInfo'][$option->option_id] = $option->option->name;
                                }
                                $result_option = [
                                    'optionCartValue' => $optionCartValue['optionCartValue'],
                                    'optionInfo' => $optionInfo['optionInfo']
                                ];
                            }

                            $create_order_product = new OrderProduct;
                            $create_order_product->order_id = $create_order->id;
                            $create_order_product->qty = $product->cart->qty;
                            $create_order_product->product_id = $product->id;
                            $create_order_product->price = $product->front_sale_price_with_taxes;
                            $create_order_product->product_name = $product->name;
                            $create_order_product->product_type = $product->product_type;
                            $create_order_product->product_image = $product->original_product->image;
                            $create_order_product->weight = $product->weight * 1000;
                            $create_order_product->tax_amount = 0;
                            $create_order_product->options = '[]';
                            $create_order_product->product_options = (count($product->cart->option) > 0) ?
                                json_encode($result_option) : '[]';
                            if ($create_order_product->save()) {
                                if ($product->with_storehouse_management) {
                                    $get_product = Product::where('id', $product->id)->first();
                                    $get_product->quantity = ($get_product->quantity - $product->cart->qty < 0) ? 0 : $get_product->quantity - $product->cart->qty;
                                    $get_product->save();
                                }
                            }
                        }

                        $create_shipment = new Shipment;
                        $create_shipment->order_id = $create_order->id;
                        $create_shipment->user_id = 0;
                        $create_shipment->cod_amount = 0;
                        $create_shipment->cod_status = ShippingCodStatusEnum::PENDING;
                        $create_shipment->weight = $total_weight * 1000;
                        $create_shipment->price = (int)$shimpent['courier_price'];
                        $create_shipment->shipping_company_name = $shimpent['courier_details']->service_display;
                        $create_shipment->shipment_id = '';
                        $create_shipment->store_id = $store->id;
                        $create_shipment->estimate_date_shipped =
                            Carbon::now()->addDay($shimpent['courier_details']->etd_thru);
                        $create_shipment->rate_id = '';
                        if ($create_shipment->save()) {
                            $create_order_address = new OrderAddress;
                            $create_order_address->name = $address->name;
                            $create_order_address->phone = $address->phone;
                            $create_order_address->email = $address->email;
                            $create_order_address->country = $address->country;
                            $create_order_address->state = $address->state;
                            $create_order_address->city = $address->city;
                            $create_order_address->address = $address->address;
                            $create_order_address->zip_code = $address->zip_code;
                            $create_order_address->order_id = $create_order->id;
                            $create_order_address->type = 'shipping_address';
                            $create_order_address->save();
                        }
                    }
                }
                $payment = (object)$request->payment;
                $customer = auth('customer')->user();
                $fee_app = $payment->fee;
                $total_all_payment = ($total_payment + $fee_app + $total_pengiriman) - $total_diskon;
                // return $total_all_payment;
                if ($payment->type === "bank_transfer") {
                    $bank_list = BankList::where('bank_code', $payment->bank_code)->first();

                    $create_payment = new Payment;
                    $create_payment->user_id = 0;
                    $create_payment->charge_id = $token;
                    $create_payment->bank = $payment->bank_code;
                    $create_payment->va_number = $bank_list->id;
                    $create_payment->payment_channel = 'bank_transfer';
                    $create_payment->status = 'pending';
                    $create_payment->currency = 'IDR';
                    $create_payment->customer_type = 'Botble\Ecommerce\Models\Customer';
                    $create_payment->payment_type = 'confirm';
                    $create_payment->customer_id = auth('customer')->id();
                    $create_payment->amount = $total_all_payment;
                    $create_payment->expiry_time = Carbon::now()->addDay(1);
                    if ($create_payment->save()) {
                        CartModel::where('customer_id', auth('customer')->id())->whereIn('id', $id_cart)->delete();
                        $get_order = Order::whereIn('id', $id_order)->get();

                        foreach ($get_order as $order) {
                            $order->payment_id = $create_payment->id;
                            $order->save();
                        }
                    }
                } else {
                    $payloads = [
                        // "title" => $token,
                        "title" => 'Belanja Produk',
                        "amount" => $total_all_payment,
                        "type" => "SINGLE",
                        "expired_date" => Carbon::now('Asia/Jakarta')->addDay(1)->format('Y-m-d H:i'),
                        "redirect_url" => route('public.checkout.success', $token),
                        // "redirect_url" => '',
                        "is_address_required" => 1,
                        "is_phone_number_required" => 1,
                        "step" => 3,
                        "sender_name" => $customer->name,
                        "sender_email" => $customer->email,
                        "sender_phone_number" => $address->phone,
                        "sender_address" => $address->address,
                        "sender_bank_type" => 'virtual_account',
                        "sender_bank" => $payment->bank_code,
                    ];

                    $flip = $this->createPayment($payloads);

                    // return $flip;
                    if (isset($flip->code) && $flip->code === 'VALIDATION_ERROR') {
                        return $response->setError(true)->setData($flip->errors);
                    } else {
                        $create_payment = new Payment;
                        $create_payment->user_id = 0;
                        $create_payment->charge_id = $flip->link_id;
                        $create_payment->bank = $flip->bill_payment->receiver_bank_account->bank_code;
                        $create_payment->va_number = $flip->bill_payment->receiver_bank_account->account_number;
                        $create_payment->payment_channel = 'virtual_account';
                        $create_payment->status = 'pending';
                        $create_payment->currency = 'IDR';
                        $create_payment->customer_type = 'Botble\Ecommerce\Models\Customer';
                        $create_payment->payment_type = 'confirm';
                        $create_payment->customer_id = auth('customer')->id();
                        $create_payment->amount = $total_all_payment;
                        $create_payment->link_payment = $flip->payment_url;
                        $create_payment->expiry_time = $flip->expired_date;
                        if ($create_payment->save()) {
                            CartModel::where('customer_id', auth('customer')->id())->whereIn('id', $id_cart)->delete();
                            $get_order = Order::whereIn('id', $id_order)->get();

                            foreach ($get_order as $order) {
                                $order->payment_id = $create_payment->id;
                                $order->save();
                            }
                        }
                    }
                }
                if(session()->has('voucher_applied')){
                    foreach(session()->get('voucher_applied') as $voucher_applied){
                        $getVoucher = DiscountClaim::where([
                            ['id',$voucher_applied['code']],
                            ['customer_id',auth('customer')->id()]
                        ])->first();

                        if($getVoucher){
                            $getVoucher->is_use = 1;
                            $getVoucher->save();
                        }
                    }
                }
                $this->resetVoucher();
                $this->afterCreateOrder($id_order);
                DB::commit();

                session()->forget('checkout_selected_cart');
                return $response->setData($token);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    protected function afterCreateOrder(string|array|null $orderIds)
    {
        $orderIds = (array)$orderIds;

        $orders = app(OrderInterface::class)->allBy([['id', 'IN', $orderIds]]);

        if (!$orders->count()) {
            return false;
        }

        foreach ($orders as $order) {
            if ($order->histories()->where('action', 'create_order')->count()) {
                return false;
            }
        }

        if (is_plugin_active('marketplace')) {
            apply_filters(SEND_MAIL_AFTER_PROCESS_ORDER_MULTI_DATA, $orders);
        } else {
            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('admin_new_order')) {
                OrderHelper::setEmailVariables($orders->first());
                $mailer->sendUsingTemplate('admin_new_order', get_admin_email()->toArray());
            }

            // Temporarily only send emails with the first order
            OrderHelper::sendOrderConfirmationEmail($orders->first(), true);
        }

        foreach ($orders as $order) {
            app(OrderHistoryInterface::class)->createOrUpdate([
                'action' => 'create_order',
                'description' => trans('plugins/ecommerce::order.new_order_from', [
                    'order_id' => $order->code,
                    'customer' => BaseHelper::clean($order->user->name ?: $order->address->name),
                ]),
                'order_id' => $order->id,
            ]);
        }

        foreach ($orders as $order) {
            foreach ($order->products as $orderProduct) {
                $product = $orderProduct->product->original_product;

                $flashSale = $product->latestFlashSales()->first();
                if (!$flashSale) {
                    continue;
                }

                $flashSale->products()->detach([$product->id]);
                $flashSale->products()->attach([
                    $product->id => [
                        'price' => $flashSale->pivot->price,
                        'quantity' => (int)$flashSale->pivot->quantity,
                        'sold' => (int)$flashSale->pivot->sold + $orderProduct->qty,
                    ],
                ]);
            }
        }

        return $orders;
    }

    private function createPayment($payloads)
    {
        $ch = curl_init();
        $secret_key = "JDJ5JDEzJFZJOEUzREhPRDBEUDB1aXo4MS5EVC4zMUJGS0Z4T1g1a1liWHB5MXlZTmNKYUJ3VFZlSmZx";

        curl_setopt($ch, CURLOPT_URL, "https://bigflip.id/big_sandbox_api/v2/pwf/bill");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloads));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    private function getCart($selected_cart)
    {

        $cartsDB = CartModel::where('customer_id', auth('customer')->id())
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

    public function beliLangsung(
        Request $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $applyPromotionsService
    ) {
        if (session()->has('buy_now')) {
            $productId = session()->get('id_product_buy_now');
            $product = Product::where([['id', $productId], ['status', BaseStatusEnum::PUBLISHED]])->first();

            if ($product && !$product->isOutOfStock()) {
                if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
                    return $response->setNextUrl(route('customer.login'));
                }

                if (!auth('customer')->check()) {
                    return $response->setNextUrl(route('customer.login'));
                }

                $qty_buy = session()->get('qty_buy_now');
                $couponDiscountAmount = 0;

                $result = $applyCouponService->execute(session()->get('applied_coupon_code'), [$productId]);
                if (!$result['error']) {
                    $couponDiscountAmount += session()->get('coupon_discount_amount');
                }

                $product->variant_config = $product->is_variation ? ProductVariation::where(
                    'product_id',
                    $productId
                )->with('productAttributes.productAttributeSet')->first() : '';
                $product->store = Store::where('id', $product->original_product->store_id)->first();
                $product->total_weight = ($product->weight / 1000) * $qty_buy;
                $product->qty_buy = $qty_buy;

                $sessionCheckoutData = [];
                $sessionCheckoutData[] = $this->processCheckoutData($sessionCheckoutData, $request);
                $voucher_applied = session()->get('voucher_applied');

                $sessionCheckoutData['voucher_applied'] = $couponDiscountAmount;

                return Theme::scope(
                    'ecommerce.buy-now',
                    compact('product', 'sessionCheckoutData'),
                    'plugins/ecommerce::themes.buy-now'
                )->render();
            }
        } else {
            return redirect(route('public.index'));
        }
    }

    public function getCheckout(
        // string $token,
        Request $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $applyPromotionsService
    ) {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if (!auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if (!session()->get('checkout_selected_cart')) {
            return $response->setNextUrl(route('public.cart'));
        }

        $sessionCheckoutData = [];
        $sessionCheckoutData[] = $this->processCheckoutData($sessionCheckoutData, $request);

        $selected_cart = explode(',', session()->get('checkout_selected_cart'));
        $carts = $this->getCart($selected_cart);
        // return $carts;
        $promotionDiscountAmount = $applyPromotionsService->execute();
        $couponDiscountAmount = 0;

        if (session()->has('applied_coupon_code')) {
            $selecteds = [];
            foreach ($selected_cart as $select) {
                if ($select != '') {
                    $selecteds[] = $select;
                }
            }
            $result = $applyCouponService->execute(session()->get('applied_coupon_code'), $selecteds);
            if (!$result['error']) {
                $couponDiscountAmount += session()->get('coupon_discount_amount');
            }
            foreach ($carts as $cart) {
                $products = $cart['products'];
                $store = $cart['store'];
                foreach ($products as $product) {
                    $result = $applyCouponService->execute(session()->get('applied_coupon_code'), [$product->cart->id], [], [], $product->cart->id);
                    // $product->cart->discount_amount = $result;
                    if (!$result['error']) {
                        $store->discount_amount = session()->get($product->cart->id . 'coupon_discount_amount');
                    }
                }
            }
        }

        session()->put('code_unique', rand(100, 999));

        $is_free_shipping = session()->get('is_free_shipping');
        $shipping_amount = session()->get('shipping_amount');
        $code_unique = session()->get('code_unique');
        $voucher_applied = session()->get('voucher_applied');

        $sessionCheckoutData['voucher_applied'] = $couponDiscountAmount;
        $sessionCheckoutData['code_unique'] = $code_unique;
        $sessionCheckoutData['promotion_discount_amount'] = $promotionDiscountAmount;
        $sessionCheckoutData['coupon_discount_amount'] = $couponDiscountAmount;
        $sessionCheckoutData['is_free_shipping'] = $is_free_shipping;
        $sessionCheckoutData['shipping_amount'] = $shipping_amount;

        // return $sessionCheckoutData;
        return view('plugins/ecommerce::orders.shipment', compact('carts', 'sessionCheckoutData'));
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

    public function getShipmentPrice(Request $request, BaseHttpResponse $response)
    {
        try {
            $zip_customer = '';
            $custAddress = '';
            if ($request->address !== 'new') {
                $address = Address::where('id', $request->address)->first();
                $zip_customer = $address->zip_code;
                $custAddress = $address;
            } else {
                $validator = Validator::make($request->all(), [
                    'new_address.address' => 'required|string',
                    'new_address.city' => 'required|string',
                    'new_address.email' => 'required|email',
                    'new_address.name' => 'required|string',
                    'new_address.phone' => 'required|string',
                    'new_address.state' => 'required|string',
                    'new_address.zip_code' => 'required|int',
                ]);
                $post_address = (object)$request->new_address;

                if ($validator->fails()) {
                    $errors = $validator->messages()->all();
                    foreach ($errors as &$error) {
                        $error = str_replace('new address.', '', $error);
                    }
                    return $response->setError(true)->setData($errors);
                } else {
                    $zip_customer = $post_address->zip_code;
                    $custAddress = $post_address;
                }
            }

            $jnt = $this->getPriceJNT($request,$response, $custAddress);

            $responses = [];
            if(count($jnt) > 0){
                foreach($jnt as $jt){
                    $res_jnt['code'] = 'J&T';
                    $res_jnt['price'] = $jt->cost;
                    $res_jnt['service_code'] = $jt->productType;
                    $res_jnt['service_display'] = 'J&T ' . $jt->name;
                    $res_jnt['etd_from'] = 3;
                    $res_jnt['etd_thru'] = 6;

                    $responses[] = $res_jnt;
                }
            }

            $jne = $this->getpPriceJNE($request, $response, $zip_customer)->price;
            if(count($jne) > 0){
                foreach($jne as $jn){
                    $jn->code = 'JNE';
                    $jn->service_display = 'JNE '.$jn->service_display;
                    $responses[] = $jn;
                }
            }

            return $response->setData($responses);
        } catch (Exception $e) {
            return $response->setError(true)->setData([$e->getMessage()]);
        }
    }

    private function getPriceJNT($request,$response, $custAddress)
    {
        $store = Store::where('id', base64_decode($request->store_id))->first();
        $storeCity = $store->city;
        $cityWithoutPrefix = strtoupper(preg_replace('/^(kota|kabupaten)\s+/i', '', $storeCity));

        $customer = JntSupport::where([
            ['kode_pos',$custAddress->zip_code]
        ])->first();

        if(!$customer){
            return $response->setError(true)->setData(['Zip code not found']);
        }

        $curl = curl_init();
        $key = 'OfmZagLbeD5B';
        $data = array(
            'weight' => "1", 'sendSiteCode' => $cityWithoutPrefix, 'destAreaCode' => $customer->kecamatan, 'cusName' => 'SOBERMART', 'productType' => ''
        );
        $data_param = json_encode($data);
        $signature = base64_encode(md5($data_param.$key));

        $payload = [
            "data" => $data_param,
            'sign' => $signature
        ];
        $queryString = http_build_query($payload);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://partner-track.jet.co.id/jandt_track/inquiry.action',
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

        $result = [];
        if($responses->is_success === "true"){
            $resultData = json_decode($responses->content);
            $result = $resultData;
        }

        return $result;
    }

    private function getpPriceJNE($request, $response, $zip_customer)
    {
        $store = Store::where('id', base64_decode($request->store_id))->first();
        $curl = curl_init();

        $customer_code = $this->getJNE($zip_customer);

        $check_zip = $this->getJNE($zip_customer);
        if (!$check_zip) {
            return $response->setError(true)->setData(['Zip code not found']);
        }

        $payload = [
            "username" => 'SOSAYAT',
            "api_key" => '3d511978f1503ce2fa85372337be5e68',
            // "from" => 'CGK10000',
            // "thru" => 'BDO10000',
            "from" => $store->origin_shipment,
            "thru" => $customer_code->tarif_code,
            "weight" => ((int)$request->weight < 1) ? 1 : $request->weight,
        ];

        $queryString = http_build_query($payload);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.jne.co.id:10205/tracing/api/pricedev',
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

        $data = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        $responses = json_decode($data);
        return $responses;
    }

    private function processCheckoutData(array $sessionData, Request $request, bool $finished = false)
    {
        if ($request->has('billing_address_same_as_shipping_address')) {
            $sessionData['billing_address_same_as_shipping_address'] = $request->input('billing_address_same_as_shipping_address');
        }

        if ($request->has('billing_address')) {
            $sessionData['billing_address'] = $request->input('billing_address');
        }

        if ($request->input('address', [])) {
            if (!isset($sessionData['created_account']) && $request->input('create_account') == 1) {
                $validator = Validator::make($request->input(), [
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password',
                    'address.email' => 'required|max:60|min:6|email|unique:ec_customers,email',
                    'address.name' => 'required|min:3|max:120',
                ]);

                if (!$validator->fails()) {
                    $customer = $this->customerRepository->createOrUpdate([
                        'name' => BaseHelper::clean($request->input('address.name')),
                        'email' => BaseHelper::clean($request->input('address.email')),
                        'phone' => BaseHelper::clean($request->input('address.phone')),
                        'password' => Hash::make($request->input('password')),
                    ]);

                    auth('customer')->attempt([
                        'email' => $request->input('address.email'),
                        'password' => $request->input('password'),
                    ], true);

                    event(new Registered($customer));

                    $sessionData['created_account'] = true;

                    $address = $this->addressRepository->createOrUpdate($request->input('address') + [
                        'customer_id' => $customer->id,
                        'is_default' => true,
                    ]);

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }

            if ($finished && auth('customer')->check() && (auth('customer')->user()->addresses()->count() == 0 || $request->input('address.address_id') == 'new')) {
                $address = $this->addressRepository->createOrUpdate($request->input('address', []) +
                    ['customer_id' => auth('customer')->id(), 'is_default' => auth('customer')->user()->addresses()->count() == 0]);

                $request->merge(['address.address_id' => $address->id]);
                $sessionData['address_id'] = $address->id;
            }
        }

        $address = null;

        $addressData = [
            'billing_address_same_as_shipping_address' => Arr::get($sessionData, 'billing_address_same_as_shipping_address', true),
            'billing_address' => Arr::get($sessionData, 'billing_address', []),
        ];

        if (!empty($address)) {
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'state' => $address->state,
                'city' => $address->city,
                'address' => $address->address,
                'zip_code' => $address->zip_code,
                'address_id' => $address->id,
            ];
        } elseif ((array)$request->input('address', [])) {
            $addressData = (array)$request->input('address', []);
        }

        foreach ($addressData as $key => $addressItem) {
            if (!is_string($addressItem)) {
                continue;
            }

            $addressData[$key] = BaseHelper::clean($addressItem);
        }

        if ($addressData && !empty($addressData['name']) && (EcommerceHelper::isPhoneFieldOptionalAtCheckout() || !empty($addressData['phone'])) && !empty($addressData['address'])) {
            $addressData['billing_address_same_as_shipping_address'] = Arr::get($sessionData, 'billing_address_same_as_shipping_address', true);
            $addressData['billing_address'] = Arr::get($sessionData, 'billing_address');
        }

        $sessionData = array_merge($sessionData, $addressData);

        return $sessionData;
    }

    protected function processOrderData(string $token, array $sessionData, Request $request, bool $finished = false): array
    {
        if ($request->has('billing_address_same_as_shipping_address')) {
            $sessionData['billing_address_same_as_shipping_address'] = $request->input('billing_address_same_as_shipping_address');
        }

        if ($request->has('billing_address')) {
            $sessionData['billing_address'] = $request->input('billing_address');
        }

        if ($request->input('address', [])) {
            if (!isset($sessionData['created_account']) && $request->input('create_account') == 1) {
                $validator = Validator::make($request->input(), [
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password',
                    'address.email' => 'required|max:60|min:6|email|unique:ec_customers,email',
                    'address.name' => 'required|min:3|max:120',
                ]);

                if (!$validator->fails()) {
                    $customer = $this->customerRepository->createOrUpdate([
                        'name' => BaseHelper::clean($request->input('address.name')),
                        'email' => BaseHelper::clean($request->input('address.email')),
                        'phone' => BaseHelper::clean($request->input('address.phone')),
                        'password' => Hash::make($request->input('password')),
                    ]);

                    auth('customer')->attempt([
                        'email' => $request->input('address.email'),
                        'password' => $request->input('password'),
                    ], true);

                    event(new Registered($customer));

                    $sessionData['created_account'] = true;

                    $address = $this->addressRepository->createOrUpdate($request->input('address') + [
                        'customer_id' => $customer->id,
                        'is_default' => true,
                    ]);

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }

            if ($finished && auth('customer')->check() && (auth('customer')->user()->addresses()->count() == 0 || $request->input('address.address_id') == 'new')) {
                $address = $this->addressRepository->createOrUpdate($request->input('address', []) +
                    ['customer_id' => auth('customer')->id(), 'is_default' => auth('customer')->user()->addresses()->count() == 0]);

                $request->merge(['address.address_id' => $address->id]);
                $sessionData['address_id'] = $address->id;
            }
        }

        $address = null;

        if ($request->input('address.address_id') && $request->input('address.address_id') !== 'new') {
            $address = $this->addressRepository->findById($request->input('address.address_id'));
            if (!empty($address)) {
                $sessionData['address_id'] = $address->id;
                $sessionData['created_order_address_id'] = $address->id;
            }
        } elseif (auth('customer')->check() && !Arr::get($sessionData, 'address_id')) {
            $address = $this->addressRepository->getFirstBy([
                'customer_id' => auth('customer')->id(),
                'is_default' => true,
            ]);

            if ($address) {
                $sessionData['address_id'] = $address->id;
            }
        }

        if (Arr::get($sessionData, 'address_id') && Arr::get($sessionData, 'address_id') !== 'new') {
            $address = $this->addressRepository->findById(Arr::get($sessionData, 'address_id'));

            if ($address) {
                $address->fill($request->input('address', []));
                $address->save();
            } else {
                $address = $this->addressRepository->createOrUpdate($request->input('address', []));
            }
        }

        $addressData = [
            'billing_address_same_as_shipping_address' => Arr::get($sessionData, 'billing_address_same_as_shipping_address', true),
            'billing_address' => Arr::get($sessionData, 'billing_address', []),
        ];

        if (!empty($address)) {
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'state' => $address->state,
                'city' => $address->city,
                'address' => $address->address,
                'zip_code' => $address->zip_code,
                'address_id' => $address->id,
            ];
        } elseif ((array)$request->input('address', [])) {
            $addressData = (array)$request->input('address', []);
        }

        foreach ($addressData as $key => $addressItem) {
            if (!is_string($addressItem)) {
                continue;
            }

            $addressData[$key] = BaseHelper::clean($addressItem);
        }

        if ($addressData && !empty($addressData['name']) && (EcommerceHelper::isPhoneFieldOptionalAtCheckout() || !empty($addressData['phone'])) && !empty($addressData['address'])) {
            $addressData['billing_address_same_as_shipping_address'] = Arr::get($sessionData, 'billing_address_same_as_shipping_address', true);
            $addressData['billing_address'] = Arr::get($sessionData, 'billing_address');
        }

        $sessionData = array_merge($sessionData, $addressData);

        if (is_plugin_active('marketplace')) {
            $products = Cart::instance('cart')->products();

            $sessionData = apply_filters(
                HANDLE_PROCESS_ORDER_DATA_ECOMMERCE,
                $products,
                $token,
                $sessionData,
                $request
            );

            OrderHelper::setOrderSessionData($token, $sessionData);

            return $sessionData;
        }

        if (!isset($sessionData['created_order'])) {
            $currentUserId = 0;
            if (auth('customer')->check()) {
                $currentUserId = auth('customer')->id();
            }

            $request->merge([
                'amount' => Cart::instance('cart')->rawTotal(),
                'user_id' => $currentUserId,
                'shipping_method' => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
                'shipping_option' => $request->input('shipping_option'),
                'shipping_amount' => 0,
                'tax_amount' => Cart::instance('cart')->rawTax(),
                'sub_total' => Cart::instance('cart')->rawSubTotal(),
                'coupon_code' => session()->get('applied_coupon_code'),
                'discount_amount' => 0,
                'status' => OrderStatusEnum::PENDING,
                'is_finished' => false,
                'token' => $token,
            ]);

            $order = $this->orderRepository->getFirstBy(compact('token'));

            $order = $this->createOrderFromData($request->input(), $order);

            $sessionData['created_order'] = true;
            $sessionData['created_order_id'] = $order->id;
        }

        if (!empty($address)) {
            $addressData['order_id'] = $sessionData['created_order_id'];
        } elseif ((array)$request->input('address', [])) {
            $addressData = array_merge(
                ['order_id' => $sessionData['created_order_id']],
                (array)$request->input('address', [])
            );
        }

        if ($addressData && !empty($addressData['name']) && (EcommerceHelper::isPhoneFieldOptionalAtCheckout() || !empty($addressData['phone'])) && !empty($addressData['address'])) {
            if (!isset($sessionData['created_order_address'])) {
                $createdOrderAddress = $this->createOrderAddress(
                    $addressData,
                    Arr::get($addressData, 'order_id')
                );
                if ($createdOrderAddress) {
                    $sessionData['created_order_address'] = true;
                    $sessionData['created_order_address_id'] = $createdOrderAddress->id;
                }
            } elseif (!empty($sessionData['created_order_id'])) {
                $this->createOrderAddress($addressData, $sessionData['created_order_id']);
            }
        }

        if (!isset($sessionData['created_order_product'])) {
            $weight = 0;
            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = $this->productRepository->findById($cartItem->id);
                if ($product) {
                    if ($product->weight) {
                        $weight += $product->weight * $cartItem->qty;
                    }
                }
            }

            $weight = EcommerceHelper::validateOrderWeight($weight);

            $this->orderProductRepository->deleteBy(['order_id' => $sessionData['created_order_id']]);

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = $this->productRepository->findById($cartItem->id);

                $data = [
                    'order_id' => $sessionData['created_order_id'],
                    'product_id' => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'product_image' => $product->original_product->image,
                    'qty' => $cartItem->qty,
                    'weight' => $weight,
                    'price' => $cartItem->price,
                    'tax_amount' => $cartItem->tax,
                    'options' => [],
                    'product_type' => $product ? $product->product_type : null,
                ];

                if ($cartItem->options->extras) {
                    $data['options'] = $cartItem->options->extras;
                }

                if ($cartItem->options['options']) {
                    $data['product_options'] = $cartItem->options['options'];
                }

                $this->orderProductRepository->create($data);
            }

            $sessionData['created_order_product'] = Cart::instance('cart')->getLastUpdatedAt();
        }

        OrderHelper::setOrderSessionData($token, $sessionData);

        return $sessionData;
    }

    protected function createOrderAddress(array $data, ?int $orderId = null)
    {
        if ($orderId) {
            $this->storeOrderBillingAddress($data, $orderId);

            return $this->orderAddressRepository->createOrUpdate($data, ['order_id' => $orderId, 'type' => OrderAddressTypeEnum::SHIPPING]);
        }

        $validator = Validator::make($data, EcommerceHelper::getCustomerAddressValidationRules());

        if ($validator->fails()) {
            return false;
        }

        $this->storeOrderBillingAddress($data);

        return $this->orderAddressRepository->create($data);
    }

    protected function storeOrderBillingAddress(array $data, ?int $orderId = null)
    {
        if (isset($data['billing_address_same_as_shipping_address']) && !$data['billing_address_same_as_shipping_address']) {
            $billingAddressData = $data['billing_address'];
            $billingAddressData['order_id'] = $orderId ?: Arr::get($data, 'order_id');
            $billingAddressData['type'] = OrderAddressTypeEnum::BILLING;

            $this->orderAddressRepository->createOrUpdate($billingAddressData, ['order_id' => $orderId, 'type' => OrderAddressTypeEnum::BILLING]);
        } else {
            $this->orderAddressRepository->deleteBy([
                'order_id' => $orderId,
                'type' => OrderAddressTypeEnum::BILLING,
            ]);
        }
    }

    public function postSaveInformation(
        string $token,
        SaveCheckoutInformationRequest $request,
        BaseHttpResponse $response,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService
    ) {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_finished' => false]);

            if (!$order) {
                return $response->setNextUrl(route('public.index'));
            }
        }

        if ($paymentMethod = $request->input('payment_method')) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));

            $sessionData = apply_filters(
                PROCESS_POST_SAVE_INFORMATION_CHECKOUT_ECOMMERCE,
                $sessionData,
                $request,
                $token
            );
        } else {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));
            OrderHelper::setOrderSessionData($token, $sessionData);
            if (session()->has('applied_coupon_code')) {
                $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
                if (empty($discount)) {
                    $removeCouponService->execute();
                }
            }
        }

        $sessionData = $this->processOrderData($token, $sessionData, $request);

        return $response->setData($sessionData);
    }

    public function getCheckoutSuccess(String $token, BaseHttpResponse $response)
    {
        if ($token) {
            //     $token = session()->get('token_checkout_success');
            $orders = $this->orderRepository->allBy([
                'token' => $token,
            ], ['address', 'products']);

            if (!$orders->count()) {
                abort(404);
            }

            if ($orders->where('is_finished', false)->count()) {
                foreach ($orders->where('is_finished', false) as $order) {
                    event(new OrderPlacedEvent($order));

                    if ($order->payment->payment_channel->label() === 'Bank transfer') {
                        $get_bank_list = BankList::where([['bank_code', $order->payment->bank], ['id', $order->payment->va_number]])->first();

                        $order->payment->va_number = $get_bank_list->bank_nomor;
                        $order->payment->bank_holder = $get_bank_list->bank_holder;
                    } else {
                        $order->payment->bank_holder = '';
                    }

                    $order->is_finished = true;
                    $order->save();

                    OrderHelper::decreaseProductQuantity($order);
                }

                OrderHelper::clearSessions($token);
            } else {
                foreach ($orders as $order) {
                    if ($order->payment->payment_channel->label() === 'Bank transfer') {
                        $get_bank_list = BankList::where([['bank_code', $order->payment->bank], ['id', $order->payment->va_number]])->first();

                        $order->payment->va_number = $get_bank_list->bank_nomor;
                        $order->payment->bank_holder = $get_bank_list->bank_holder;
                    } else {
                        $order->payment->bank_holder = '';
                    }
                }
            }

            return view('plugins/marketplace::orders.thank-you', compact('orders'));
        } else {
            return redirect(route('public.index'));
        }
    }

    public function postApplyCoupon(
        ApplyCouponRequest $request,
        HandleApplyCouponService $handleApplyCouponService,
        BaseHttpResponse $response
    ) {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }
        $result = [
            'error' => false,
            'message' => '',
        ];

        if ($request->selected_cart === null) {
            return $response
                ->setError(true)
                ->withInput()
                ->setMessage('Pilih barang dulu untuk bisa hemat dengan berbagai promo terbaik');
        }

        $selected = explode(',', $request->selected_cart);
        $selecteds = [];
        foreach ($selected as $select) {
            if ($select != '') {
                $selecteds[] = $select;
            }
        }

        $result = $handleApplyCouponService->execute($request->input('coupon_code'), $selecteds);

        // if (is_plugin_active('marketplace')) {
        //     $result = apply_filters(HANDLE_POST_APPLY_COUPON_CODE_ECOMMERCE, $result, $request);
        // } else {
        //     $result = $handleApplyCouponService->execute($request->input('coupon_code'));
        // }

        if ($result['error']) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        $couponCode = $request->input('coupon_code');

        return $response
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function postRemoveCoupon(
        Request $request,
        HandleRemoveCouponService $removeCouponService,
        BaseHttpResponse $response
    ) {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        // if (is_plugin_active('marketplace')) {
        //     $products = Cart::instance('cart')->products();
        //     $result = apply_filters(HANDLE_POST_REMOVE_COUPON_CODE_ECOMMERCE, $products, $request);
        // } else {
        // }
        $selected = explode(',', $request->selected_cart);
        $selecteds = [];
        foreach ($selected as $select) {
            if ($select != '') {
                $selecteds[] = $select;
            }
        }
        $result = $removeCouponService->execute($selecteds);

        if ($result['error']) {
            if ($request->ajax()) {
                return $result;
            }

            return $response
                ->setError()
                ->setData($result)
                ->setMessage($result['message']);
        }

        return $response
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    public function getCheckoutRecover(string $token, Request $request, BaseHttpResponse $response)
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_RECOVER_ECOMMERCE, $token, $request);
        }

        $order = $this->orderRepository
            ->getFirstBy([
                'token' => $token,
                'is_finished' => false,
            ], [], ['products', 'address']);

        if (!$order) {
            abort(404);
        }

        if (session()->has('tracked_start_checkout') && session('tracked_start_checkout') == $token) {
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        } else {
            session(['tracked_start_checkout' => $token]);
            $sessionCheckoutData = [
                'name' => $order->address->name,
                'email' => $order->address->email,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'country' => $order->address->country,
                'state' => $order->address->state,
                'city' => $order->address->city,
                'zip_code' => $order->address->zip_code,
                'shipping_method' => $order->shipping_method,
                'shipping_option' => $order->shipping_option,
                'shipping_amount' => $order->shipping_amount,
            ];
        }

        Cart::instance('cart')->destroy();
        foreach ($order->products as $orderProduct) {
            $request->merge(['qty' => $orderProduct->qty]);

            $product = $this->productRepository->findById($orderProduct->product_id);
            if ($product) {
                OrderHelper::handleAddCart($product, $request);
            }
        }

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        return $response->setNextUrl(route('public.checkout.information', $token))
            ->setMessage(__('You have recovered from previous orders!'));
    }

    protected function createOrderFromData(array $data, ?Order $order): Order|null|false
    {
        $data['is_finished'] = false;

        if ($order) {
            $order->fill($data);
            $order = $this->orderRepository->createOrUpdate($order);
        } else {
            $order = $this->orderRepository->createOrUpdate($data);
        }

        if (!$order->referral()->count()) {
            $referrals = app(FootprinterInterface::class)->getFootprints();

            if ($referrals) {
                $order->referral()->create($referrals);
            }
        }

        return $order;
    }
}
