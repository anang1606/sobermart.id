<?php

Route::group(['namespace' => 'Botble\Marketplace\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'marketplaces', 'as' => 'marketplace.'], function () {
            Route::group(['prefix' => 'stores', 'as' => 'store.'], function () {
                Route::resource('', 'StoreController')->parameters(['' => 'store']);
                Route::delete('items/destroy', [
                    'as' => 'deletes',
                    'uses' => 'StoreController@deletes',
                    'permission' => 'marketplace.store.destroy',
                ]);

                Route::get('view/{id}', [
                    'as' => 'view',
                    'uses' => 'StoreRevenueController@view',
                ]);

                Route::group(['prefix' => 'revenues', 'as' => 'revenue.'], function () {
                    Route::match(['GET', 'POST'], 'list/{id}', [
                        'as' => 'index',
                        'uses' => 'StoreRevenueController@index',
                        'permission' => 'marketplace.store.view',
                    ]);

                    Route::post('create/{id}', [
                        'as' => 'create',
                        'uses' => 'StoreRevenueController@store',
                    ]);
                });
            });

            Route::group(['prefix' => 'withdrawals', 'as' => 'withdrawal.'], function () {
                Route::resource('', 'WithdrawalController')
                    ->parameters(['' => 'withdrawal'])
                    ->except([
                        'create',
                        'store',
                        'destroy',
                    ]);
            });
            Route::group(['prefix' => 'member', 'as' => 'member.'], function () {
                Route::get('/withdrawal', [
                    'as' => 'withdrawal',
                    'uses' => 'WithdrawalController@member',
                ]);

                Route::post('/withdrawal', [
                    'as' => 'withdrawal',
                    'uses' => 'WithdrawalController@member',
                ]);

                Route::get('/withdrawal/edit/{id}', [
                    'as' => 'withdrawal.edit',
                    'uses' => 'WithdrawalController@memberEdit',
                ]);

                Route::post('/withdrawal/edit/{id}', [
                    'as' => 'withdrawal.edit',
                    'uses' => 'WithdrawalController@memberPost',
                ]);
            });

            Route::group(['prefix' => 'support-message', 'as' => 'support-message.'], function () {
                Route::get('', [
                    'as' => 'index',
                    'uses' => 'SupportMessageController@index',
                ]);

                Route::get('/details', [
                    'as' => 'details',
                    'uses' => 'SupportMessageController@details',
                ]);

                Route::post('/details', [
                    'as' => 'details',
                    'uses' => 'SupportMessageController@store',
                ]);
            });

            Route::group(['prefix' => 'request-gift', 'as' => 'request-gift.'], function () {
                Route::get('', [
                    'as' => 'index',
                    'uses' => 'RequestGiftController@index',
                ]);

                Route::post('', [
                    'as' => 'index',
                    'uses' => 'RequestGiftController@index',
                ]);

                Route::get('edit/{id}', [
                    'as' => 'edit',
                    'uses' => 'RequestGiftController@edit',
                ]);

                Route::post('edit/{id}', [
                    'as' => 'edit',
                    'uses' => 'RequestGiftController@update',
                ]);
            });

            Route::group(['prefix' => 'paket', 'as' => 'paket_master.'], function () {
                Route::get('', [
                    'as' => 'index',
                    'uses' => 'PaketMaster@index',
                    'permission' => 'payment.index',
                ]);

                Route::post('', [
                    'as' => 'index',
                    'uses' => 'PaketMaster@index',
                    'permission' => 'payment.index',
                ]);

                Route::get('/create', [
                    'as' => 'create',
                    'uses' => 'PaketMaster@create',
                    'permission' => 'payment.index',
                ]);

                Route::post('/create', [
                    'as' => 'create',
                    'uses' => 'PaketMaster@store',
                    'permission' => 'payment.index',
                ]);

                Route::get('/edit/{id}', [
                    'as' => 'edit',
                    'uses' => 'PaketMaster@edit',
                    'permission' => 'payment.index',
                ]);

                Route::get('/member/{id}', [
                    'as' => 'view',
                    'uses' => 'PaketMaster@viewMember',
                    'permission' => 'payment.index',
                ]);
                Route::post('/member/{id}', [
                    'as' => 'view',
                    'uses' => 'PaketMaster@viewMember',
                    'permission' => 'payment.index',
                ]);

                Route::post('/edit/{id}', [
                    'as' => 'edit',
                    'uses' => 'PaketMaster@update',
                    'permission' => 'payment.index',
                ]);

                Route::delete('/delete/{id}', [
                    'as' => 'destroy',
                    'uses' => 'PaketMaster@destroy',
                    'permission' => 'payment.index',
                ]);
            });

            Route::get('settings', [
                'as' => 'settings',
                'uses' => 'MarketplaceController@getSettings',
            ]);

            Route::post('settings', [
                'as' => 'settings.post',
                'uses' => 'MarketplaceController@postSettings',
                'permission' => 'marketplace.settings',
            ]);

            Route::group(['prefix' => 'unverified-vendors', 'as' => 'unverified-vendors.'], function () {
                Route::match(['GET', 'POST'], '/', [
                    'as' => 'index',
                    'uses' => 'UnverifiedVendorController@index',
                ]);

                Route::get('view/{id}', [
                    'as' => 'view',
                    'uses' => 'UnverifiedVendorController@view',
                    'permission' => 'marketplace.unverified-vendors.edit',
                ]);

                Route::post('approve/{id}', [
                    'as' => 'approve-vendor',
                    'uses' => 'UnverifiedVendorController@approveVendor',
                    'permission' => 'marketplace.unverified-vendors.edit',
                ]);
            });

            Route::group(['prefix' => 'ahli-waris', 'as' => 'ahli-waris.'], function () {
                Route::match(['GET', 'POST'], '/', [
                    'as' => 'index',
                    'uses' => 'MarketplaceController@ahliWaris',
                ]);
            });

            Route::group(['prefix' => BaseHelper::getAdminPrefix() . '/marketplaces'], function () {
                Route::group(['prefix' => 'vendors', 'as' => 'vendors.'], function () {
                    Route::match(['GET', 'POST'], '/', [
                        'as' => 'index',
                        'uses' => 'VendorController@index',
                    ]);
                });
            });
        });

        Route::group(['prefix' => BaseHelper::getAdminPrefix() . '/ecommerce'], function () {
            Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
                Route::post('approve-product/{id}', [
                    'as' => 'approve-product',
                    'uses' => 'ProductController@approveProduct',
                    'permission' => 'products.edit',
                ]);
            });
        });
    });
});
