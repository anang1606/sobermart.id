<?php

Route::group(['namespace' => 'Botble\AdsCategory\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'category-ads', 'as' => 'category-ads.'], function () {
            Route::resource('', 'AdsCategoryController')->parameters(['' => 'category-ads']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'AdsCategoryController@deletes',
                'permission' => 'ads.destroy',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            Route::get('adsbanner/{key}', [
                'as' => 'public.adsbanner',
                'uses' => 'PublicController@getAdsClick',
            ]);
        });
    }
});
