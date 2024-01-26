<?php
Route::group([
    'namespace' => '\Haruncpi\LaravelUserActivity\Controllers',
    'middleware' => config('user-activity.middleware')
    ], function () {
    Route::get(config('user-activity.route_path'), 'ActivityController@getIndex')->name('user-activity');
    Route::post(config('user-activity.route_path'), 'ActivityController@handlePostRequest');
});
