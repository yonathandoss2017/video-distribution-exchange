<?php

Route::group(['prefix' => '/request', 'as' => 'request.'], function () {
    Route::get('/ready', 'DppController@readyRequest')->name('ready');
    Route::get('/new', 'DppController@newRequest')->name('new.index');
    Route::resource('/{playlist_id}/manage/{entry_id}/scene', 'DppSceneController');
});
Route::resource('/request', 'DppController', ['only' => [
    'show', 'update',
]]);
