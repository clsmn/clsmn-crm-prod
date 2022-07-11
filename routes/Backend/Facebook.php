<?php

/**
 * All route names are prefixed with 'admin.'.
 */

Route::group(['namespace' => 'Facebook', 'as' => 'fb.'], function () {
    Route::group([
        'middleware' => 'access.routeNeedsRole:Manager',
    ], function () {
         Route::get('fb/getCampaign', 'FacebookLeadsController@index')->name('index');
         Route::get('ajax/fb/refreshCompaignList', 'FacebookLeadsController@refreshCompaignList')->name('refresh');
    });
    
    Route::post('ajax/fb/updateCompaignData', 'FacebookLeadsController@updateCompaignData')->name('update');
    // Route::get('fb/updateSource', 'FacebookLeadsController@updateSourceName')->name('index');
    // Route::get('ajax/fb/refreshCompaignAds', 'FacebookLeadsController@refreshCompaignAds')->name('refresh.ads');
    // Route::get('ajax/fb/refreshCompaignList', 'FacebookLeadsController@refreshCompaignList')->name('refresh');

   
});
