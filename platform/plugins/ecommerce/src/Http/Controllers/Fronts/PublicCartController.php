<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Http\Requests\CartRequest;
use Botble\Ecommerce\Http\Requests\UpdateCartRequest;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Cart;
use EcommerceHelper;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use OrderHelper;
use SeoHelper;
use Botble\Ecommerce\Models\Cart as CartModel;
use Botble\Ecommerce\Models\Product;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\OptionValue;
use Illuminate\Http\Request;
use Theme;

use Botble\Ecommerce\Services\HandleApplyCouponService;

class PublicCartController extends Controller
{
    protected ProductInterface $productRepository;
    protected HandleApplyCouponService $handleApplyCouponService;

    public function __construct(ProductInterface $productRepository,HandleApplyCouponService $handleApplyCouponService)
    {
        $this->productRepository = $productRepository;
        $this->handleApplyCouponService = $handleApplyCouponService;
    }

    public function store(CartRequest $request, BaseHttpResponse $response)
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        $product = $this->productRepository->findById($request->input('id'));

        if (!$product) {
            return $response
                ->setError()
                ->setMessage(__('This product is out of stock or not exists!'));
        }

        if ($product->variations->count() > 0 && !$product->is_variation) {
            $product = $product->defaultVariation->product;
        }

        if ($product->isOutOfStock()) {
            return $response
                ->setError()
                ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name ?: $product->name]));
        }

        $maxQuantity = $product->quantity;

        if (!$product->canAddToCart($request->input('qty', 1))) {
            return $response
                ->setError()
                ->setMessage(__('Maximum quantity is :max!', ['max' => $maxQuantity]));
        }

        $product->quantity -= $request->input('qty', 1);

        $outOfQuantity = false;
        $qty_with_cart = 0;

        $customer_id = auth('customer')->id();
        if ($product->is_variation) {
            $attributes = ProductVariation::where('product_id', $product->id)->first();
            $get_cart = CartModel::where([['customer_id', $customer_id], ['product_id', $product->id], ['attributes', $attributes->id]])->first();
        } else {
            $get_cart = CartModel::where([['customer_id', $customer_id], ['product_id', $product->id]])->first();
        }
        if ($get_cart) {
            $originalQuantity = $product->quantity;
            $product->quantity = (int)$product->quantity - $get_cart->qty;

            if ($product->quantity < 0) {
                $product->quantity = 0;
            }

            if ($product->isOutOfStock()) {
                $outOfQuantity = true;
            }

            $product->quantity = $originalQuantity;
            $qty_with_cart = (int)$request->input('qty', 1) + $get_cart->qty;
        }

        if ($request->hidden_flashsale_status === '1') {
            $original_id = $product->original_product->id;
            $get_flash_sale = \DB::select("SELECT * FROM ec_flash_sale_products WHERE product_id = '" . $original_id . "'");
            if (count($get_flash_sale) > 0) {
                $stock_sale = (int)$get_flash_sale[0]->quantity - (int)$get_flash_sale[0]->sold;
                if ((int)$request->input('qty', 1) > (int)$stock_sale || $qty_with_cart > (int)$stock_sale) {
                    return $response
                        ->setError()
                        ->setMessage("Quantity exceeds flash sale stock. Please adjust the quantity accordingly. Thank you.");
                }
            }
        }

        if ($product->original_product->options()->where('required', true)->exists()) {
            if (!$request->input('options')) {
                return $response
                    ->setError()
                    ->setData(['next_url' => $product->original_product->url])
                    ->setMessage(__('Please select product options!'));
            }

            $requiredOptions = $product->original_product->options()->where('required', true)->get();

            $message = null;

            foreach ($requiredOptions as $requiredOption) {
                if (!$request->input('options.' . $requiredOption->id . '.values')) {
                    $message .= trans('plugins/ecommerce::product-option.add_to_cart_value_required', ['value' => $requiredOption->name]);
                }
            }

            if ($message) {
                return $response
                    ->setError()
                    ->setMessage(__('Please select product options!'));
            }
        }

        if ($outOfQuantity) {
            return $response
                ->setError()
                ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name ?: $product->name]));
        }
        // if ($request->input('checkout')) {
        //     $nextUrl = route('public.checkout.beli-langsung');

        //     $options_request = ($request->input('options')) ? json_encode($request->input('options')) : '';
        //     //set session
        //     session()->put('buy_now',true);
        //     session()->put('id_product_buy_now',$request->id);
        //     session()->put('options_request_buy_now',$options_request);
        //     session()->put('qty_buy_now',$request->qty);

        //     $response->setData(['next_url' => $nextUrl]);

        //     if ($request->ajax() && $request->wantsJson()) {
        //         return $response;
        //     }

        //     return $response
        //         ->setNextUrl($nextUrl);
        // }
        // if (EcommerceHelper::getQuickBuyButtonTarget() == 'cart') {
            // $nextUrl = route('public.cart');
        // }

        if ($request->input('checkout')) {
            $nextUrl = route('public.checkout.information');

            // $options_request = ($request->input('options')) ? json_encode($request->input('options')) : '';
            // //set session
            // session()->put('buy_now',true);
            // session()->put('id_product_buy_now',$request->id);
            // session()->put('options_request_buy_now',$options_request);
            // session()->put('qty_buy_now',$request->qty);
            $cartItems = OrderHelper::handleAddCart($product, $request,true);

            $response->setData(['next_url' => $nextUrl]);

            if ($request->ajax() && $request->wantsJson()) {
                return $response;
            }

            return $response
                ->setNextUrl($nextUrl);
        }

        $cartItems = OrderHelper::handleAddCart($product, $request);

        $response
            ->setMessage(__(
                'Added product :product to cart successfully!',
                ['product' => $product->original_product->name ?: $product->name]
            ));

        // $token = OrderHelper::getOrderSessionToken();

        // $nextUrl = route('public.checkout.information', $token);

        return $response
            ->setData([
                'status' => true,
                // 'count' => Cart::instance('cart')->count(),
                // 'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
                'content' => $cartItems,
            ]);
    }

    public function getView(Request $request,HandleApplyPromotionsService $applyPromotionsService)
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        Theme::asset()
            ->container('footer')
            ->add('ecommerce-checkout-js', 'vendor/core/plugins/ecommerce/js/checkout.js', ['jquery']);

        $promotionDiscountAmount = 0;
        $couponDiscountAmount = 0;

        $products = [];
        $crossSellProducts = collect();

        SeoHelper::setTitle(__('Shopping Cart'));
        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Shopping Cart'), route('public.cart'));

        $cart_selecteds = [];
        $carts = CartModel::where([['customer_id', auth('customer')->id()],['is_buynow',0]])->has('product')
        ->whereHas('product', function ($query) {
            // $query->whereNotNull('store_id');
        })->get();

        // return $carts;
        foreach ($carts as $cart) {
            $product = Product::where([['id', $cart->product_id], ['status', BaseStatusEnum::PUBLISHED]])->first();
            // return $product;
            if ($product && $product->original_product->store_id) {
                $promotionDiscountAmount = $applyPromotionsService->execute();

                $sessionData = OrderHelper::getOrderSessionData();

                if (session()->has('applied_coupon_code') && $request->applied_coupon === '1') {
                    $couponDiscountAmount = session()->get('coupon_discount_amount');
                    $cart_selecteds = session()->get('cart_selected');
                }else if($request->applied_coupon === '0'){
                    $cart_selecteds = session()->get('cart_selected');
                }else{
                    session()->forget('applied_coupon_code');
                    session()->forget('cart_selected');
                    session()->forget('is_free_shipping');
                    session()->forget('shipping_amount');
                    session()->forget('checkout_selected_cart');
                }
                // return session()->get('applied_coupon_code');
                $parentIds = $product->pluck('id')->toArray();
                $crossSellProducts = get_cart_cross_sale_products($parentIds, theme_option('number_of_cross_sale_product', 4));

                $product->variant_config = $product->is_variation ? ProductVariation::where(
                    'product_id',
                    $cart->product_id
                )->with('productAttributes.productAttributeSet')->first() : '';

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

                        $options[] = $option;
                    }
                    $cart->option = $options;
                } else {
                    $cart->option = [];
                }
                $cart->product = $product;
                // $products[] = $product;
            }
        }
        // return $carts;
        return Theme::scope(
            'ecommerce.cart',
            compact(
                'promotionDiscountAmount',
                'cart_selecteds',
                'couponDiscountAmount',
                'carts',
                'products',
                'crossSellProducts'
            ),
            'plugins/ecommerce::themes.cart'
        )->render();
    }

    public function postUpdate(UpdateCartRequest $request, BaseHttpResponse $response)
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }


        $data = (object)$request->input();

        $outOfQuantity = false;
        if ($data) {
            $cartItem =
                CartModel::where([
                    ['customer_id', auth('customer')->id()],
                    ['id', $data->rowId],
                    ['product_id', $data->productId],
                ])->first();

            $product = null;

            $product = $this->productRepository->findById($cartItem->product_id);

            if ($product) {
                $originalQuantity = $product->quantity;
                $product->quantity = (int)$product->quantity - (int)$data->qty;

                if ($product->quantity < 0) {
                    $product->quantity = 0;
                }

                if ($product->isOutOfStock()) {
                    $outOfQuantity = true;
                } else {
                    // Cart::instance('cart')->update($item['rowId'], Arr::get($item, 'values'));
                    $cartItem->qty = (int)$data->qty;
                    $cartItem->save();
                }

                $product->quantity = $originalQuantity;
            }
        }

        if ($outOfQuantity) {
            return $response
                ->setError()
                ->setData([
                    'count' => 0,
                    'total_price' => 0,
                    'content' => [],
                ])
                ->setMessage(__('One or all products are not enough quantity so cannot update!'));
        }

        return $response
            ->setData([
                'count' => 0,
                'total_price' => 0,
                'content' => [],
            ])
            ->setMessage(__('Update cart successfully!'));
    }

    public function getRemove(string $id, BaseHttpResponse $response)
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        try {
            // Cart::instance('cart')->remove($id);
            CartModel::where('id', $id)->delete();
        } catch (Exception) {
            return $response->setError()->setMessage(__('Cart item is not existed!'));
        }

        return $response
            ->setData([
                'count' => Cart::instance('cart')->count(),
                'total_price' => format_price(Cart::instance('cart')->rawSubTotal()),
                'content' => Cart::instance('cart')->content(),
            ])
            ->setMessage(__('Removed item from cart successfully!'));
    }

    public function getDestroy(BaseHttpResponse $response)
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        Cart::instance('cart')->destroy();

        return $response
            ->setData(Cart::instance('cart')->content())
            ->setMessage(__('Empty cart successfully!'));
    }

    public function prosesCheckOut(Request $request)
    {
        $selected = explode(',', $request->selected_cart);
        $selecteds = [];
        foreach ($selected as $select) {
            if ($select != '') {
                $selecteds[] = $select;
            }
        }
        session()->put('checkout_selected_cart', implode(',', $selecteds));
        session()->forget('is_free_shipping');
        session()->forget('shipping_amount');
        session()->forget('applied_coupon_code');
        session()->forget('coupon_discount_amount');
        session()->forget('voucher_applied');
        return redirect(route('public.checkout.information'));
    }

    public function calcTotal(
        Request $request,
        BaseHttpResponse $response
    )
    {
        $cart = CartModel::where([['id', $request->_exp], ['customer_id', auth('customer')->id()]])->first();
        $subTotal = 0;
        $couponDiscountAmount = 0;
        if ($cart) {
            $product = Product::where('id', $cart->product_id)->first();

            $price_option = 0;

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
                        $price_option += ($product->front_sale_price_with_taxes * $option->affect_price) / 100;
                    } else {
                        $price_option += $option->affect_price;
                    }
                }
            }
            if (session()->has('applied_coupon_code')) {
                $result = $this->handleApplyCouponService->execute(session()->get('applied_coupon_code'),[$request->_exp]);
                if(!$result['error']){
                    $couponDiscountAmount = session()->get('coupon_discount_amount');
                }
            }
            $subTotal = ($product->front_sale_price_with_taxes + $price_option) * $cart->qty;
        }
        return $response
        ->setData([
            'subTotal' => $subTotal,
            'couponDiscountAmount' => $couponDiscountAmount,
        ]);
    }
}
