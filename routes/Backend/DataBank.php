<?php

/**
 * All route names are prefixed with 'admin.'.
 */

Route::group(['namespace' => 'DataBank', 'as' => 'data.bank.'], function () {
    Route::group([
        'middleware' => 'access.routeNeedsRole:Manager',
    ], function () {
        Route::get('data/bank', 'DataBankController@index')->name('index');
        Route::get('data/bank/update', 'DataBankController@updateFromLead')->name('updateFromLead');
    });
    Route::get('data/bank/create', 'DataBankController@create')->name('create');
    

    Route::get('data/bank/bulk/create', 'DataBankController@bulkCreate')->name('bulk');
    Route::post('data/bank/bulk/create', 'DataBankController@bulkCreate')->name('bulk.post');
    Route::put('data/bank/bulk/create', 'DataBankController@bulkSave')->name('bulk.save');
    Route::post('data/bank/bulk/upload_data', 'DataBankController@upload_data')->name('bulk.upload_data');
    Route::post('data/bank/bulk/create_new', 'DataBankController@bulkSave_new')->name('bulk.savenew');
    Route::post('data/bank', 'DataBankController@store')->name('store');
    Route::post('data/bank/get', 'DataBankTableController@getDataBankUsers')->name('get');
    //Route::get('data/bank/get/download', 'DataBankTableController@getDataBankUsersDownload')->name('download');
    Route::post('data/bank/search', 'DataBankTableController@searchDataBankUsers')->name('search');

    Route::post('ajax/moveToLead', 'DataBankController@moveToLead');
    Route::post('ajax/moveToLead1', 'DataBankController@moveToLead1');
});
