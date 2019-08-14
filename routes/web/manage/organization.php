<?php

Route::get('/', 'Manage\DashboardController@selectProperty')->name('manage.property.select');
Route::get('/organization/select/{id}', 'Manage\DashboardController@selectOrganization')->name('manage.organization.select');

// Manage Property
Route::group(['prefix' => '/property/add', 'as' => 'manage.property.'], function () {
    Route::get('/', 'Manage\DashboardController@addProperty')->name('add');
    Route::post('/', 'Manage\DashboardController@storeProperty')->name('store');
});
// Manage Users
Route::get('/users', 'Manage\UserController@index')->name('manage.users');
Route::group(['prefix' => '/users', 'as' => 'manage.user.'], function () {
    Route::post('/', 'Manage\UserController@store')->name('store');
    Route::get('/add', 'Manage\UserController@create')->name('add');
    Route::get('/{user}/edit', 'Manage\UserController@edit')->name('edit');
    Route::patch('/{user}', 'Manage\UserController@update')->name('update');
    Route::delete('/{user}', 'Manage\UserController@destroy')->name('destroy');
});
// Manage > Settings:
Route::group(['prefix' => '/settings', 'as' => 'manage.settings'], function () {
    Route::get('/', 'Manage\SettingsController@index');
    Route::post('/update', 'Manage\SettingsController@updateSetting')->name('.update');
});
// Manage > Profile
Route::group(['prefix' => '/profile', 'as' => 'manage.profile'], function () {
    Route::get('/', 'Manage\ProfileController@profileSetting');
    Route::post('/update', 'Manage\ProfileController@updateProfile')->name('.update');
});

Route::group(['middleware' => 'can:organization-admin', 'prefix' => '/organization', 'as' => 'manage.organization.', 'namespace' => 'Manage\Organization'], function () {
    Route::group(['prefix' => '/playlists', 'as' => 'playlists.'], function () {
        Route::get('/', 'PlaylistController@index')->name('index');
        Route::delete('/', 'PlaylistController@destroy')->name('destroy');
        Route::get('/{playlist}/publish', 'PlaylistController@publish')->name('publish.show');
        Route::put('/{playlist}/publish', 'PlaylistController@updatePublish')->name('publish.update');
    });

    Route::group(['prefix' => '/videos', 'as' => 'videos.'], function () {
        Route::get('/', 'VideoController@index')->name('index');
        Route::delete('/delete/bulk', 'VideoController@bulkDelete')->name('delete.bulk');
        Route::post('/export/bulk', 'VideoController@bulkExport')->name('export.bulk');
        Route::get('/download/{id}', 'VideoController@download')->name('download');
        Route::delete('/delete/{id}', 'VideoController@delete')->name('delete');
    });

    Route::group(['prefix' => '/request-logs', 'as' => 'request-logs.'], function () {
        Route::get('/', 'RequestLogController@index')->name('index');
        Route::get('/playlist/{playlist_id}/show', 'RequestLogController@showPlaylist')->name('playlist.show');
        Route::post('/{playlist_id}/approve', 'RequestLogController@approve')->name('approve');
        Route::post('/{playlist_id}/reject', 'RequestLogController@reject')->name('reject');
        Route::post('/approve/bulk', 'RequestLogController@bulkApprove')->name('approve.bulk');
        Route::post('/reject/bulk', 'RequestLogController@bulkReject')->name('reject.bulk');
        Route::get('/{playlist_id}/comment', 'RequestLogController@commentEdit')->name('comment.edit');
        Route::put('/{playlist_id}/comment', 'RequestLogController@commentStore')->name('comment.store');
    });
});
