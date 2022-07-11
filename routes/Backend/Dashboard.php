<?php

/**
 * All route names are prefixed with 'admin.'.
 */
Route::get('dashboard', 'DashboardController@index')->name('dashboard');
Route::post('dashboard', 'DashboardController@getCalculation')->name('getCalculation');
Route::post('Updatesubscription', 'DashboardController@Updatesubscription')->name('Updatesubscription');
Route::get('check_in', 'DashboardController@checkIn')->name('check_in');
Route::get('fetch/leads', 'DashboardController@fetchLeads')->name('fetch.leads');
Route::get('check_out', 'DashboardController@checkOut')->name('check_out');
Route::get('lead/upload', 'DashboardController@leadUpload')->name('lead.upload');
Route::get('lead/leadUploadByDate', 'DashboardController@leadUploadByDate')->name('lead.leadUploadByDate');
Route::get('lead/leadUploadDetails', 'DashboardController@leadUploadDetails')->name('lead.leadUploadDetails');
Route::get('lead/getuploadleadByfilter', 'DashboardController@getuploadleadByfilter')->name('lead.getuploadleadByfilter');
Route::get('lead/performance', 'DashboardController@leadPerformance')->name('lead.performance');
Route::get('lead/getleadPerformance', 'DashboardController@getleadPerformance')->name('lead.getleadPerformance');
Route::get('lead/getleadPerformanceByDate', 'DashboardController@getleadPerformanceByDate')->name('lead.getleadPerformanceByDate');
Route::get('lead/getleadPerformanceByfilter', 'DashboardController@getleadPerformanceByfilter')->name('lead.getleadPerformanceByfilter');
Route::get('otp', 'DashboardController@otp')->name('otp');
Route::get('hmvisits', 'DashboardController@hmvisits')->name('hmvisits');
Route::post('reset/default/password', 'DashboardController@resetPassword');
Route::get('lead/calltest', 'DashboardController@calltest')->name('lead.calltest');
Route::get('lead/getCallResponse', 'DashboardController@getCallResponse')->name('lead.getCallResponse');

//Report Routes
Route::get('reports/sales', 'DashboardController@salesReport')->name('reports.sales');
Route::get('reports/leads', 'DashboardController@leadsReport')->name('reports.leads');
Route::get('reports/sales/today', 'DashboardController@todaySales')->name('reports.sales.today');
Route::get('reports/saleData', 'DashboardController@saleData')->name('reports.saleData');
Route::get('/lead/getSalesByDate', 'DashboardController@getSalesByDate')->name('lead.getSalesByDate');

//Report Lead convertion
Route::get('lead/getleadConversionByDate', 'DashboardController@getleadConversionByDate')->name('lead.getleadConversionByDate');



Route::get('lead/expiringPlan', 'DashboardController@getOldleads')->name('reports.expiringPlan');

Route::get('lead/expiringPlanReSale', 'DashboardController@getOldleadsReSale')->name('reports.expiringPlanReSale');


Route::get('reports/subscriptions', 'DashboardController@subscriptions')->name('reports.subscriptions');


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