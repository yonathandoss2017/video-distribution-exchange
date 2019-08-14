<?php
/*
|--------------------------------------------------------------------------
| SPMobile API Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| w.r.t SPMobile API.
|
*/

Route::group(['prefix' => '/sp-mobile', 'namespace' => '\Api\V1\SPMobile', 'middleware' => ['is_service_provider'], 'as' => 'sp-mobile.'], function () {
    Route::get('/playlists/', 'PlaylistController@index')->name('playlist');
    Route::get('/playlist/{id}/videos/', 'PlaylistController@withVideos')->where('id', '[0-9]+')->name('playlist.videos');
});
