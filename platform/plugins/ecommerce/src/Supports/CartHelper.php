<?php

namespace Botble\Ecommerce\Supports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Cart;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\OptionValue;

class CartHelper
{
    public function getAllDataCart()
    {
        $cart = [];
        if(auth('customer')->check()){
            $getCarts = Cart::where([['customer_id',auth('customer')->id()],['is_buynow',0]])->get();
            foreach($getCarts as $cartItem){
                $product = Product::where([['id',$cartItem->product_id],['status',BaseStatusEnum::PUBLISHED]])->first();
                if($product){
                    $product->variant_config = $product->is_variation ? ProductVariation::where('product_id',
                    $cartItem->product_id)->with('productAttributes.productAttributeSet')->first() : '';

                    if ($cartItem->options !== '') {
                        $options = explode(';', $cartItem->options);
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
                        $cartItem->option = $options;
                    } else {
                        $cartItem->option = [];
                    }

                    $cartItem->product = $product;

                    $cart[] = $cartItem;
                }
            }
        }
        return $cart;
    }
}
