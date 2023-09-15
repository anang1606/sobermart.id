<?php

Route::group(['namespace' => 'Botble\Payment\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'payments/methods', 'permission' => 'payments.settings'], function () {
            Route::get('', [
                'as' => 'payments.methods',
                'uses' => 'PaymentController@methods',
            ]);

            Route::post('settings', [
                'as' => 'payments.settings',
                'uses' => 'PaymentController@updateSettings',
                'middleware' => 'preventDemo',
            ]);

            Route::post('', [
                'as' => 'payments.methods.post',
                'uses' => 'PaymentController@updateMethods',
                'middleware' => 'preventDemo',
            ]);

            Route::post('update-status', [
                'as' => 'payments.methods.update.status',
                'uses' => 'PaymentController@updateMethodStatus',
                'middleware' => 'preventDemo',
            ]);

        });

        Route::group(['prefix' => 'payments/accumulation', 'as' => 'accumulation.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'AccumulationController@index',
                'permission' => 'payment.index',
            ]);

            Route::post('', [
                'as' => 'index',
                'uses' => 'AccumulationController@index',
                'permission' => 'payment.index',
            ]);
        });

        Route::group(['prefix' => 'payments/paket', 'as' => 'payment.'], function () {
            Route::get('', [
                'as' => 'paket',
                'uses' => 'PaymentController@paket',
                'permission' => 'payment.index',
            ]);

            Route::post('', [
                'as' => 'paket',
                'uses' => 'PaymentController@paket',
                'permission' => 'payment.index',
            ]);
        });

        Route::group(['prefix' => 'payments/bank', 'as' => 'bank.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'PaymentController@bank',
                'permission' => 'payment.index',
            ]);

            Route::get('create', [
                'as' => 'create',
                'uses' => 'PaymentController@createBank',
                'permission' => 'payment.index',
            ]);

            Route::get('edit/{id}', [
                'as' => 'show',
                'uses' => 'PaymentController@editBank',
                'permission' => 'payment.index',
            ]);

            Route::put('update/{id}', [
                'as' => 'update',
                'uses' => 'PaymentController@updateBank',
                'permission' => 'payment.index',
            ]);

            Route::delete('destroy/{id}', [
                'as' => 'destroy',
                'uses' => 'PaymentController@destroyBank',
                'permission' => 'payment.index',
            ]);

            Route::post('store', [
                'as' => 'store',
                'uses' => 'PaymentController@storeBank',
                'permission' => 'payment.index',
            ]);

            Route::post('', [
                'as' => 'index',
                'uses' => 'PaymentController@bank',
                'permission' => 'payment.index',
            ]);
        });

        Route::group(['prefix' => 'payments/transactions', 'as' => 'payment.'], function () {
            Route::resource('', 'PaymentController')->parameters(['' => 'payment'])->only(['index', 'destroy']);

            Route::get('{chargeId}', [
                'as' => 'show',
                'uses' => 'PaymentController@show',
                'permission' => 'payment.index',
            ]);

            Route::put('{chargeId}', [
                'as' => 'update',
                'uses' => 'PaymentController@update',
                'permission' => 'payment.index',
            ]);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'PaymentController@deletes',
                'permission' => 'payment.destroy',
            ]);

            Route::get('refund-detail/{id}/{refundId}', [
                'as' => 'refund-detail',
                'uses' => 'PaymentController@getRefundDetail',
                'permission' => 'payment.index',
            ]);
        });
    });
});
