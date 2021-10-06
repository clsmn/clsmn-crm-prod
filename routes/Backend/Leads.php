<?php

/**
 * All route names are prefixed with 'admin.lead'.
 */
Route::group([
    'as'         => 'lead.',
    'namespace'  => 'Lead',
], function () {

    /*
     * Lead Management
     */
    //Route::resource('lead', 'LeadController');
    Route::get('lead', 'LeadController@index')->name('index');
    Route::group([
        'middleware' => 'access.routeNeedsRole:Manager',
    ], function () {
        Route::get('lead/call_history', 'LeadController@callHistory')->name('call_history');
    });
    Route::get('lead/{lead}', 'LeadController@getLead')->name('get');
    Route::get('ajax/setPrimaryNumber', 'LeadController@setPrimaryNumber');
    Route::post('ajax/addAlternateNumber/{lead}', 'LeadController@addAlternateNumber');
    Route::post('ajax/updateAlternateNumber/{alternateNumber}', 'LeadController@updateAlternateNumber');
    Route::delete('ajax/deleteAlternateNumber/{alternateNumber}', 'LeadController@deleteAlternateNumber');
    
    Route::post('ajax/updateLeadAddress/{lead}', 'LeadController@updateLeadAddress');
    Route::post('ajax/updateLead/{lead}', 'LeadController@updateLead');
    Route::delete('ajax/removeChild/{child}', 'LeadController@removeChild');
    Route::post('ajax/updateDataChild/{child}', 'LeadController@updateDataChild');
    Route::post('ajax/addDataChild/{lead}', 'LeadController@addDataChild');
    Route::get('ajax/getLeadDetail/{lead}', 'LeadController@getLeadDetail');
    Route::post('ajax/assignLeadToAnother', 'LeadController@assignLeadToAnother');

    //Call Lead
    Route::post('ajax/callLead/{lead}', 'LeadController@callLead');
    Route::post('ajax/cloudCallLead/{lead}', 'LeadController@cloudCallLead');
    Route::post('ajax/updateCallLead/{callHistory}', 'LeadController@updateCallLead');
    Route::get('ajax/fetchCloudCall/{callHistory}', 'LeadController@fetchCloudCall');
    Route::get('ajax/cloudCallStatus/{callHistory}', 'LeadController@cloudCallStatus');

    //Add lead note
    Route::post('ajax/addLeadNote/{lead}', 'LeadController@addLeadNote');

    //For learning Subscription
    Route::post('ajax/activateLearning/{lead}', 'LeadController@activateLearning');
    Route::get('ajax/getLearningChildren/{lead}', 'LeadController@getLearningChildren');
    Route::post('ajax/startLearningTrial/{lead}', 'LeadController@startLearningTrial');

    //For DataTables
    Route::post('lead/call/get', 'LeadTableController@callList')->name('call.get');
    Route::post('lead/follow_ups', 'LeadTableController@followUpList')->name('call.follow_up.get');
    Route::post('lead/call/transferred', 'LeadTableController@transferredList')->name('call.transferred');
    Route::post('lead/called/get', 'LeadTableController@calledList')->name('called.get');
    Route::post('lead/called/manager/get', 'LeadTableController@calledManagerList')->name('called.manager.get');
    Route::post('lead/search', 'LeadTableController@searchLead')->name('search');

    Route::group([
        'middleware' => 'access.routeNeedsRole:Manager',
    ], function () {
        Route::get('assigned/leads', 'LeadController@listAssignedLeads')->name('assigned');
        Route::post('assigned/leads/get', 'LeadTableController@leadList')->name('assigned.get');
    });
});