<div class="shop-body">
    <div class="shop-body-content-wrapper">
        <div class="shop-body-content__left">
            <div class="shop-products-wrapper">
                @foreach ($products as $product)
                    @include('plugins/ecommerce::orders.partials.shop.product', compact('product'))
                @endforeach
            </div>
        </div>
        <div class="shop-body-content__right">
            @include(
                'plugins/ecommerce::orders.partials.shop.courier',
                compact('total_weight', 'store', 'sessionCheckoutData'))
        </div>
    </div>
</div>
