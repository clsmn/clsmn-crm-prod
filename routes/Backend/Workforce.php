<?php

/**
 * All route names are prefixed with 'admin.workforce'.
 */
Route::group([
    'as'         => 'workforce.',
    'namespace'  => 'Workforce',
    'middleware' => 'access.routeNeedsRole:Manager',
], function () {
    /*
    * Workforce Management
    */
    Route::get('workforce', 'WorkforceController@index')->name('index');
    Route::get('workforce/executive/{user}', 'WorkforceController@executive')->name('executive');
    Route::post('ajax/setExecutiveNote', 'WorkforceController@setExecutiveNote');

    //For DataTables
    Route::post('workforce/get', 'WorkforceTableController')->name('get');
});