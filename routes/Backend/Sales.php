<?php

/**
 * All route names are prefixed with 'admin.'.
 */

Route::group(['namespace' => 'Sales', 'as' => 'sales.'], function () {
    Route::group([
        'middleware' => 'access.routeNeedsPermission:sales-page',
    ], function () {
         Route::get('sales/delight', 'DelightSalesController@index')->name('index');
    });
    
    Route::post('ajax/sales/updateDelightSale', 'DelightSalesController@updateDelightSale')->name('update');
    Route::get('sales/assignDelightSale', 'DelightSalesController@assignDelightSale')->name('assign');
   
});
