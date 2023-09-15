<?php

Route::group(['namespace' => 'Botble\Shortcode\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'short-codes'], function () {
            Route::post('ajax-get-admin-config/{key}', [
                'as' => 'short-codes.ajax-get-admin-config',
                'uses' => 'ShortcodeController@ajaxGetAdminConfig',
                'permission' => false,
            ]);
            Route::post('get_category_2', [
                'as' => 'short-codes.get-category-2',
                'uses' => 'ShortcodeController@getCategory2',
                'permission' => false,
            ]);
            Route::post('get_category_3', [
                'as' => 'short-codes.get-category-3',
                'uses' => 'ShortcodeController@getCategory3',
                'permission' => false,
            ]);
        });
    });
});
