<?php

Route::group(['namespace' => 'Botble\PopupAds\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'popup-ads', 'as' => 'popup-ads.'], function () {
            Route::resource('', 'PopupAdsController')->parameters(['' => 'popup-ads']);
            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'PopupAdsController@deletes',
                'permission' => 'ads.destroy',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
            Route::get('popupsbanner/{key}', [
                'as' => 'public.popupsbanner',
                'uses' => 'PublicController@getAdsClick',
            ]);
            Route::post('fetch-banner', [
                'as' => 'public.fetchBannerPopups',
                'uses' => 'PublicController@fetchBannerPopups',
            ]);
        });
    }
});
