<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers\Fronts', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::get('cart', [
            'as' => 'public.cart',
            'uses' => 'PublicCartController@getView',
        ]);

        Route::post('cart/add-to-cart', [
            'as' => 'public.cart.add-to-cart',
            'uses' => 'PublicCartController@store',
        ]);

        Route::post('cart/proses-checkout', [
            'as' => 'public.cart.proses-checkout',
            'uses' => 'PublicCartController@prosesCheckOut',
        ]);

        Route::post('cart/update', [
            'as' => 'public.cart.update',
            'uses' => 'PublicCartController@postUpdate',
        ]);

        Route::post('cart/calc-total', [
            'as' => 'public.cart.calc-total',
            'uses' => 'PublicCartController@calcTotal',
        ]);

        Route::get('cart/remove/{id}', [
            'as' => 'public.cart.remove',
            'uses' => 'PublicCartController@getRemove',
        ]);

        Route::get('cart/destroy', [
            'as' => 'public.cart.destroy',
            'uses' => 'PublicCartController@getDestroy',
        ]);
    });
});