<?php

Route::group([
    'prefix'     => 'search',
    'as'         => 'search.',
    'namespace'  => 'Search',
    'middleware' => 'access.routeNeedsRole:Manager',
], function () {
    /*
     * Search Specific Functionality
     */
    Route::get('/', 'SearchController@index')->name('index');
});
