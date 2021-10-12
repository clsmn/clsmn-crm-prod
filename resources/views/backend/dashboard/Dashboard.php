<?php

/**
 * All route names are prefixed with 'admin.'.
 */
Route::get('dashboard', 'DashboardController@index')->name('dashboard');

Route::get('check_in', 'DashboardController@checkIn')->name('check_in');
Route::get('fetch/leads', 'DashboardController@fetchLeads')->name('fetch.leads');
Route::get('check_out', 'DashboardController@checkOut')->name('check_out');
Route::get('lead/upload', 'DashboardController@leadUpload')->name('lead.upload');
Route::get('lead/leadUploadByDate', 'DashboardController@leadUploadByDate')->name('lead.leadUploadByDate');
Route::get('lead/performance', 'DashboardController@leadPerformance')->name('lead.performance');
Route::get('lead/getleadPerformance', 'DashboardController@getleadPerformance')->name('lead.getleadPerformance');
Route::get('lead/getleadPerformanceByDate', 'DashboardController@getleadPerformanceByDate')->name('lead.getleadPerformanceByDate');
Route::get('lead/getleadPerformanceByfilter', 'DashboardController@getleadPerformanceByfilter')->name('lead.getleadPerformanceByfilter');

Route::get('otp', 'DashboardController@otp')->name('otp');
Route::post('reset/default/password', 'DashboardController@resetPassword');

Route::group(['namespace' => 'User', 'as' => 'user.'], function () {

    /*
     * User Account Specific
     */
    Route::get('account', 'AccountController@index')->name('account');

    /*
     * User Profile Specific
     */
    Route::patch('profile/update', 'ProfileController@update')->name('profile.update');
});