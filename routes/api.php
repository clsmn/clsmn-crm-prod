<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/acr/webhook/{user}' , 'Api\WebhookController@acrWebhook');
Route::post('/lead' , 'Api\WebhookController@addLead');
Route::post('/lead/phase' , 'Api\WebhookController@updateLeadPhase');
Route::post('/subscription' , 'Api\WebhookController@subscriptionPurchased');

Route::post('/login' , 'Api\AuthController@login');

Route::get('/app/version' , 'Api\DashboardController@appVersion');

Route::middleware('auth:api')->group(function(){
    Route::get('/dashboard' , 'Api\DashboardController@index');

    Route::get('/track/status' , 'Api\LeadController@trackStatus');
    Route::get('/filters' , 'Api\LeadController@filters');
    Route::get('/checkIn' , 'Api\LeadController@checkIn');
    Route::get('/checkOut' , 'Api\LeadController@checkOut');

    Route::get('/call/list' , 'Api\LeadController@callList');
    Route::get('/follow_up/list' , 'Api\LeadController@followUpList');
    Route::get('/call/history' , 'Api\LeadController@callHistory');

    Route::get('/search/lead' , 'Api\LeadController@searchLead');
    Route::get('/lead/{lead}' , 'Api\LeadController@leadDetail');
    Route::get('/lead/{lead}/history' , 'Api\LeadController@leadHistory');

    Route::post('/addLeadNote/{lead}' , 'Api\LeadController@addLeadNote');
    
    Route::get('/callLead/{lead}' , 'Backend\Lead\LeadController@callLead');
    Route::get('/cloudCallLead/{lead}' , 'Backend\Lead\LeadController@cloudCallLead');
    Route::post('/updateCallLead/{callHistory}' , 'Backend\Lead\LeadController@updateCallLead');
    Route::get('/fetchCloudCall/{callHistory}' , 'Backend\Lead\LeadController@fetchCloudCall');
    Route::get('/cloudCallStatus/{callHistory}' , 'Backend\Lead\LeadController@cloudCallStatus');

    Route::get('/setPrimaryNumber', 'Backend\Lead\LeadController@setPrimaryNumber');
    Route::post('/addAlternateNumber/{lead}', 'Backend\Lead\LeadController@addAlternateNumber');
    Route::post('/updateAlternateNumber/{alternateNumber}', 'Backend\Lead\LeadController@updateAlternateNumber');
    Route::delete('/deleteAlternateNumber/{alternateNumber}', 'Backend\Lead\LeadController@deleteAlternateNumber');
    Route::post('/updateLead/{lead}', 'Backend\Lead\LeadController@updateLead');
});
