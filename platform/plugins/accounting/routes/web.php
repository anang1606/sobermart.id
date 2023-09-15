<?php

Route::group(['namespace' => 'Botble\Accounting\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'accounting/coa', 'as' => 'coa.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'CoaController@index',
                'permission' => 'accounting.index',
            ]);

            Route::post('', [
                'as' => 'index',
                'uses' => 'CoaController@index',
                'permission' => 'accounting.index',
            ]);
        });

        Route::group(['prefix' => 'accounting/saldo-coa', 'as' => 'coa-saldo.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'CoaSaldoController@index',
                'permission' => 'accounting.index',
            ]);

            Route::post('', [
                'as' => 'index',
                'uses' => 'CoaSaldoController@index',
                'permission' => 'accounting.index',
            ]);

            Route::get('/{tahun}/{idcoa}', [
                'as' => 'edit',
                'uses' => 'CoaSaldoController@edit',
                'permission' => 'accounting.index',
            ]);

            Route::post('/{tahun}/{idcoa}', [
                'as' => 'update',
                'uses' => 'CoaSaldoController@update',
                'permission' => 'accounting.index',
            ]);
        });

        Route::group(['prefix' => 'accounting/buku-besar', 'as' => 'buku-besar.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'BukuBesarController@index',
                'permission' => 'accounting.index',
            ]);

            Route::post('/{prefix}', [
                'as' => 'view',
                'uses' => 'BukuBesarController@view',
                'permission' => 'accounting.index',
            ]);
        });

        Route::group(['prefix' => 'accounting/ongkos-kirim', 'as' => 'ongkos-kirim.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'OngkosKirimController@index',
                'permission' => 'accounting.index',
            ]);

            Route::post('/{prefix}', [
                'as' => 'view',
                'uses' => 'OngkosKirimController@view',
                'permission' => 'accounting.index',
            ]);
        });

        Route::group(['prefix' => 'accounting/neraca-lajur', 'as' => 'neraca-lajur.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'NeracaLajurController@index',
                'permission' => 'accounting.index',
            ]);

            Route::post('/{prefix}', [
                'as' => 'view',
                'uses' => 'NeracaLajurController@view',
                'permission' => 'accounting.index',
            ]);
        });

        Route::group(['prefix' => 'accounting/rugi-laba', 'as' => 'rugi-laba.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'LabaRugiCrontoller@index',
                'permission' => 'accounting.index',
            ]);
        });

        Route::group(['prefix' => 'accounting/posisi-keuangan', 'as' => 'posisi-keuangan.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'PosisiKeuanganCrontoller@index',
                'permission' => 'accounting.index',
            ]);
            Route::get('/data', [
                'as' => 'data',
                'uses' => 'PosisiKeuanganCrontoller@data',
                'permission' => 'accounting.index',
            ]);
        });

        Route::group(['prefix' => 'accounting/expense', 'as' => 'expense.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'ExpenseCrontoller@index',
                'permission' => 'accounting.index',
            ]);
            Route::post('', [
                'as' => 'index',
                'uses' => 'ExpenseCrontoller@index',
                'permission' => 'accounting.index',
            ]);

            Route::get('/create', [
                'as' => 'create',
                'uses' => 'ExpenseCrontoller@create',
                'permission' => 'accounting.index',
            ]);
            Route::post('/create', [
                'as' => 'create',
                'uses' => 'ExpenseCrontoller@store',
                'permission' => 'accounting.index',
            ]);
        });
    });
});
