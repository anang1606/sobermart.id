<?php

// Custom routes
Route::group(['namespace' => 'Theme\Farmart\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::group([
            'prefix' => 'ajax',
            'as' => 'public.ajax.',
        ], function () {
            Route::controller('FarmartController')->group(function () {
                Route::get('search-products', [
                    'uses' => 'ajaxSearchProducts',
                    'as' => 'search-products',
                ]);

                Route::get('products', [
                    'uses' => 'ajaxGetProducts',
                    'as' => 'products',
                ]);

                Route::get('featured-product-categories', [
                    'uses' => 'ajaxGetFeaturedProductCategories',
                    'as' => 'featured-product-categories',
                ]);

                Route::get('featured-brands', [
                    'uses' => 'ajaxGetFeaturedBrands',
                    'as' => 'featured-brands',
                ]);

                Route::get('get-flash-sale/{id}', [
                    'uses' => 'ajaxGetFlashSale',
                    'as' => 'get-flash-sale',
                ]);

                Route::post('get-category-level-1', [
                    'uses' => 'getCategorySub',
                    'as' => 'get-category-level-1',
                ]);

                Route::get('product-categories/products', [
                    'uses' => 'ajaxGetProductsByCategoryId',
                    'as' => 'product-category-products',
                ]);

                Route::get('featured-products', [
                    'uses' => 'ajaxGetFeaturedProducts',
                    'as' => 'featured-products',
                ]);

                Route::get('cart', [
                    'uses' => 'ajaxCart',
                    'as' => 'cart',
                ]);

                Route::get('quick-view/{id?}', [
                    'uses' => 'ajaxGetQuickView',
                    'as' => 'quick-view',
                ]);

                Route::post('add-to-wishlist/{id?}', [
                    'uses' => 'ajaxAddProductToWishlist',
                    'as' => 'add-to-wishlist',
                ]);

                Route::get('related-products/{id}', [
                    'uses' => 'ajaxGetRelatedProducts',
                    'as' => 'related-products',
                ]);

                Route::get('product-reviews/{id}', [
                    'uses' => 'ajaxGetProductReviews',
                    'as' => 'product-reviews',
                ]);

                Route::get('get-product-categories', [
                    'uses' => 'ajaxGetProductCategories',
                    'as' => 'get-product-categories',
                ]);

                Route::get('recently-viewed-products', [
                    'uses' => 'ajaxGetRecentlyViewedProducts',
                    'as' => 'recently-viewed-products',
                ]);

                Route::post('ajax/contact-seller', 'ajaxContactSeller')
                    ->name('contact-seller');
            });
        });
    });
});

Theme::routes();

Route::group(['namespace' => 'Theme\Farmart\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::get('/', 'FarmartController@getIndex')
            ->name('public.index');

        Route::get('sitemap.xml', 'FarmartController@getSiteMap')
            ->name('public.sitemap');

        Route::get('{slug?}' . config('core.base.general.public_single_ending_url'), 'FarmartController@getView')
            ->name('public.single');
    });

    // Route::get('member', 'MemberController@index')->name('public.member');
    // Route::get('/api/payments/midtrans-notification', 'PaymentController@getIndex');
    // Route::post('/api/payments/midtrans-notification', 'PaymentController@postStore');
    Route::get('/api/payments/flip-acc-payment/view', 'PaymentController@getAccPayment');
    Route::post('/api/payments/flip-acc-payment', 'PaymentController@postAccPayment');
    Route::post('/api/payments/detail-payment', 'PaymentController@detailsPayment');

    //custom api
    Route::get('/api/product/{id}', 'ApiController@productDetails');
    Route::get('/api/image/{image}', 'ApiController@getImage');
    Route::get('/api/vendor/{id}', 'ApiController@getVendor');
    Route::get('/api/vendor_unix/{id}', 'ApiController@getVendorById');
    Route::get('/api/manual-payment-bank', 'ApiController@manualPaymentBank');
});
