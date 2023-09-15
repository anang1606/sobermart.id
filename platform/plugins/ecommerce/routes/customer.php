<?php

Route::group(
    ['namespace' => 'Botble\Ecommerce\Http\Controllers\Customers', 'middleware' => ['web', 'core']],
    function () {
        Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
            Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
                Route::resource('', 'CustomerController')->parameters(['' => 'customer']);

                Route::delete('items/destroy', [
                    'as' => 'deletes',
                    'uses' => 'CustomerController@deletes',
                    'permission' => 'customers.destroy',
                ]);
            });

            Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
                Route::get('get-list-customers-for-select', [
                    'as' => 'get-list-customers-for-select',
                    'uses' => 'CustomerController@getListCustomerForSelect',
                    'permission' => 'customers.index',
                ]);

                Route::get('get-list-customers-for-search', [
                    'as' => 'get-list-customers-for-search',
                    'uses' => 'CustomerController@getListCustomerForSearch',
                    'permission' => ['customers.index', 'orders.index'],
                ]);

                Route::post('update-email/{id}', [
                    'as' => 'update-email',
                    'uses' => 'CustomerController@postUpdateEmail',
                    'permission' => 'customers.edit',
                ]);

                Route::get('get-customer-addresses/{id}', [
                    'as' => 'get-customer-addresses',
                    'uses' => 'CustomerController@getCustomerAddresses',
                    'permission' => ['customers.index', 'orders.index'],
                ]);

                Route::get('get-customer-order-numbers/{id}', [
                    'as' => 'get-customer-order-numbers',
                    'uses' => 'CustomerController@getCustomerOrderNumbers',
                    'permission' => ['customers.index', 'orders.index'],
                ]);


                Route::get('view/{id}', [
                    'as' => 'view',
                    'uses' => 'CustomerController@viewMember',
                    'permission' => ['customers.index', 'orders.index'],
                ]);

                Route::get('belanja/{id}', [
                    'as' => 'belanja',
                    'uses' => 'CustomerController@viewBelanja',
                    'permission' => ['customers.index', 'orders.index'],
                ]);

                Route::post('create-customer-when-creating-order', [
                    'as' => 'create-customer-when-creating-order',
                    'uses' => 'CustomerController@postCreateCustomerWhenCreatingOrder',
                    'permission' => ['customers.index', 'orders.index'],
                ]);

                Route::post('verify-email/{id}', [
                    'as' => 'verify-email',
                    'uses' => 'CustomerController@verifyEmail',
                    'permission' => 'customers.index',
                ]);

            });
        });
    }
);

if (defined('THEME_MODULE_SCREEN_NAME')) {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::group([
            'namespace' => 'Botble\Ecommerce\Http\Controllers\Customers',
            'middleware' => ['web', 'core', 'customer.guest'],
            'as' => 'customer.',
        ], function () {
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login')->name('login.post');

            Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
            Route::post('checkRefferal', 'RegisterController@checkRefferal')->name('checkRefferal');
            Route::post('register', 'RegisterController@register')->name('register.post');

            Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.request');
            Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset.post');
            Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
            Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')
                ->name('password.reset.update');

            Route::get('verify', 'RegisterController@getVerify')
                ->name('verify');
        });

        Route::group([
            'namespace' => 'Botble\Ecommerce\Http\Controllers\Customers',
            'middleware' => [
                'web',
                'core',
                EcommerceHelper::isEnableEmailVerification() ? 'customer' : 'customer.guest',
            ],
            'as' => 'customer.',
        ], function () {
            Route::get('register/confirm/resend', 'RegisterController@resendConfirmation')
                ->name('resend_confirmation');
            Route::get('register/confirm/{user}', 'RegisterController@confirm')
                ->name('confirm');
        });

        Route::group([
            'namespace' => 'Botble\Ecommerce\Http\Controllers\Customers',
            'middleware' => ['web', 'core', 'customer'],
            'prefix' => 'customer',
            'as' => 'customer.',
        ], function () {
            Route::get('logout', 'LoginController@logout')->name('logout');

            Route::get('overview', [
                'as' => 'overview',
                'uses' => 'PublicController@getOverview',
            ]);

            Route::get('edit-account', [
                'as' => 'edit-account',
                'uses' => 'PublicController@getEditAccount',
            ]);

            Route::post('edit-account', [
                'as' => 'edit-account.post',
                'uses' => 'PublicController@postEditAccount',
            ]);

            Route::post('upload-payment', [
                'as' => 'upload-payment',
                'uses' => 'PublicController@uploadPayment',
            ]);

            Route::get('change-password', [
                'as' => 'change-password',
                'uses' => 'PublicController@getChangePassword',
            ]);

            Route::post('change-password', [
                'as' => 'post.change-password',
                'uses' => 'PublicController@postChangePassword',
            ]);

            Route::get('payments', [
                'as' => 'payments',
                'uses' => 'PublicController@getListPayments',
            ]);

            Route::get('chats', [
                'as' => 'chats',
                'uses' => 'PublicController@getChat',
            ]);

            Route::get('orders', [
                'as' => 'orders',
                'uses' => 'PublicController@getListOrders',
            ]);

            Route::get('orders/view/{id}', [
                'as' => 'orders.view',
                'uses' => 'PublicController@getViewOrder',
            ]);

            Route::get('order/cancel/{id}', [
                'as' => 'orders.cancel',
                'uses' => 'PublicController@getCancelOrder',
            ]);

            Route::get('order/confirm/{id}', [
                'as' => 'orders.confirm',
                'uses' => 'PublicController@getConfirmOrder',
            ]);

            Route::get('return/confirm/{id}', [
                'as' => 'returns.confirm',
                'uses' => 'PublicController@getConfirmReturn',
            ]);

            Route::get('address', [
                'as' => 'address',
                'uses' => 'PublicController@getListAddresses',
            ]);

            Route::get('address/create', [
                'as' => 'address.create',
                'uses' => 'PublicController@getCreateAddress',
            ]);

            Route::post('address/create', [
                'as' => 'address.create.post',
                'uses' => 'PublicController@postCreateAddress',
            ]);

            Route::get('address/edit/{id}', [
                'as' => 'address.edit',
                'uses' => 'PublicController@getEditAddress',
            ]);

            Route::post('address/edit/{id}', [
                'as' => 'address.edit.post',
                'uses' => 'PublicController@postEditAddress',
            ]);

            Route::get('address/delete/{id}', [
                'as' => 'address.destroy',
                'uses' => 'PublicController@getDeleteAddress',
            ]);

            Route::get('orders/print/{id}', [
                'as' => 'print-order',
                'uses' => 'PublicController@getPrintOrder',
            ]);

            Route::post('avatar', [
                'as' => 'avatar',
                'uses' => 'PublicController@postAvatar',
            ]);

            Route::get('order-returns', [
                'as' => 'order_returns',
                'uses' => 'PublicController@getListReturnOrders',
            ]);

            Route::get('order-returns/detail/{id}', [
                'as' => 'order_returns.detail',
                'uses' => 'PublicController@getDetailReturnOrder',
            ]);

            Route::get('order-returns/request/{order_id}', [
                'as' => 'order_returns.request_view',
                'uses' => 'PublicController@getReturnOrder',
            ]);

            Route::post('order-returns/send-request', [
                'as' => 'order_returns.send_request',
                'uses' => 'PublicController@postReturnOrder',
            ]);

            Route::get('downloads', [
                'as' => 'downloads',
                'uses' => 'PublicController@getDownloads',
            ]);

            Route::get('download/{id}', [
                'as' => 'downloads.product',
                'uses' => 'PublicController@getDownload',
            ]);

            Route::get('ahli-waris', [
                'as' => 'ahli-waris',
                'uses' => 'PublicController@ahliWaris',
            ]);

            Route::get('ahli-waris/create', [
                'as' => 'ahli-waris.create',
                'uses' => 'PublicController@createAhliWaris',
            ]);

            Route::get('ahli-waris/edit/{id}', [
                'as' => 'ahli-waris.edit',
                'uses' => 'PublicController@editAhliWaris',
            ]);
            Route::post('ahli-waris/update/{id}', [
                'as' => 'ahli-waris.update',
                'uses' => 'PublicController@storeAhliWaris',
            ]);
            Route::get('ahli-waris/delete/{id}', [
                'as' => 'ahli-waris.destroy',
                'uses' => 'PublicController@deleteAhliWaris',
            ]);
            Route::post('ahli-waris/{id}', [
                'as' => 'ahli-waris.store',
                'uses' => 'PublicController@storeAhliWaris',
            ]);

            Route::group([
                'prefix' => 'invoices',
                'as' => 'invoices.',
            ], function () {
                Route::resource('', 'InvoiceController')
                    ->only('index')
                    ->parameters('invoices');
                Route::get('{id}', 'InvoiceController@show')->name('show');
                Route::get('{id}/generate-invoice', 'InvoiceController@getGenerateInvoice')->name('generate_invoice');
            });

            Route::get('product-reviews', [
                'as' => 'product-reviews',
                'uses' => 'PublicController@getProductReviews',
            ]);

            Route::get('member-list', [
                'as' => 'member-list',
                'uses' => 'PublicController@getMemberDownline',
            ]);

            Route::post('member-list', [
                'as' => 'member-list',
                'uses' => 'PublicController@storeWithdrawal',
            ]);

            Route::get('withdrawal/show/{id}', [
                'as' => 'withdrawal.show',
                'uses' => 'PublicController@withdrawalShow',
            ]);

            Route::get('withdrawal/show/form/{id}', [
                'as' => 'withdrawal.show.form',
                'uses' => 'PublicController@withdrawalShowForm',
            ]);

            Route::get('join-member', [
                'as' => 'member',
                'uses' => 'MemberController@index',
                'permission' => 'customers.index',
            ]);

            Route::get('join/{id}/{referral}', [
                'as' => 'join',
                'uses' => 'MemberController@store',
                'permission' => 'customers.index',
            ]);

            Route::get('member/paket/{id}', [
                'as' => 'paket.active',
                'uses' => 'MemberController@active',
                'permission' => 'customers.index',
            ]);

            Route::post('member/get_referral', [
                'as' => 'paket.get_referral',
                'uses' => 'MemberController@getReferral',
                'permission' => 'customers.index',
            ]);

            Route::get('history', [
                'as' => 'history',
                'uses' => 'PublicController@getListHistory',
                'permission' => ['customers.index', 'orders.index'],
            ]);

            Route::get('notif/read/{id}', [
                'as' => 'notification.read',
                'uses' => 'PublicController@handleReadAndDirect',
            ]);

            Route::get('notif', [
                'as' => 'notification',
                'uses' => 'PublicController@notifications',
            ]);

            Route::get('voucher-wallet', [
                'as' => 'voucher-wallet',
                'uses' => 'VoucherController@index',
            ]);

            Route::get('cashback-voucher', [
                'as' => 'cashback-voucher',
                'uses' => 'VoucherController@list',
            ]);

            Route::get('cashback-voucher/{code}', [
                'as' => 'cashback-voucher.claim',
                'uses' => 'VoucherController@claim',
            ]);

            Route::get('gift-target', [
                'as' => 'gift-target',
                'uses' => 'GiftController@index',
            ]);

            Route::get('verified-data', [
                'as' => 'verified-data',
                'uses' => 'GiftController@verifiedData',
            ]);

            Route::post('verified-data', [
                'as' => 'verified-data',
                'uses' => 'GiftController@storeVerifiedData',
            ]);

            Route::get('gift-target/claim', [
                'as' => 'gift-target.claim',
                'uses' => 'GiftController@store',
            ]);

            Route::get('bantuan', [
                'as' => 'bantuan',
                'uses' => 'PublicController@bantuan',
            ]);

            Route::post('bantuan', [
                'as' => 'bantuan',
                'uses' => 'PublicController@storeBantuan',
            ]);
        });
    });
}

Route::group([
    'namespace' => 'Botble\Ecommerce\Http\Controllers\Customers',
    'middleware' => ['web', 'core'],
    'prefix' => 'customer',
    'as' => 'customer.',
], function () {
    Route::get('list-paket', [
        'as' => 'member',
        'uses' => 'MemberController@listAllPaket',
        'permission' => 'customers.index',
    ]);
});
