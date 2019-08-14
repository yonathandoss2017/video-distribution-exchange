<?php

Route::group(['middleware' => ['check_sp', 'can:manage-property,property'], 'prefix' => '/{property}/sp', 'as' => 'manage.sp.', 'namespace' => 'Manage\SP'], function () {
    // Manage > SP > Content > Playlists
    Route::group(['prefix' => '/playlists'], function () {
        Route::get('/', 'PlaylistController@index')->name('playlists.index');
        Route::get('/{playlist}/edit', 'PlaylistController@edit')->name('playlist.edit');
        Route::put('/{playlist}', 'PlaylistController@update')->name('playlist.update');
        Route::delete('/{playlist}', 'PlaylistController@destroy')->name('playlists.destroy');
    });

    // Manage > SP > Content > Videos
    Route::group(['prefix' => '/videos'], function () {
        Route::get('/', 'VideoController@index')->name('videos');
        Route::group(['as' => 'video.'], function () {
            Route::get('/edit/{id}', 'VideoController@edit')->name('edit');
            Route::post('/edit/{id}', 'VideoController@update')->name('update');
            Route::get('{id}/download', 'VideoController@download')->name('download');
            Route::post('/download/bulk', 'VideoController@bulkDownload')->name('download.bulk');
        });
    });

    // Manage > SP > Syndications
    Route::resource('/syndication', 'IVST\SyndicationController');

    // Manage > SP > Settings
    Route::group(['prefix' => '/settings'], function () {
        Route::get('/property', 'PropertyController@propertySettings')->name('settings.property');
        Route::post('/property', 'PropertyController@updateProperty')->name('update.property_settings');

        Route::get('/notifications', 'PropertyController@notification')->name('settings.notifications');
        Route::put('/notifications/{user}', 'PropertyController@updateNotification')->name('settings.update_notifications');
    });

    // Manage > SP > Request Logs
    Route::group(['prefix' => '/request-log', 'as' => 'request-log.'], function () {
        Route::get('/', 'Exchange\RequestLogController@index')->name('index');
        Route::get('/{requestLog}', 'Exchange\RequestLogController@show')->name('show');
    });

    Route::group(['prefix' => 'terms-of-distributions', 'as' => 'tod.'], function () {
        Route::get('/{id}/region-right/{regionRightId}', 'TermsOfDistributionController@regionRight')->name('regionRight');
        Route::get('/', 'TermsOfDistributionController@index')->name('index');
        Route::get('/{id}', 'TermsOfDistributionController@show')->name('show');
        Route::put('/{id}/accept', 'TermsOfDistributionController@accept')->name('accept');
        Route::delete('/delete', 'TermsOfDistributionController@delete')->name('delete');
    });

    // Manage > SP > Analytics
    Route::group(['prefix' => '/analytics', 'as' => 'analytics.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'AnalyticsController@index']);
    });
});
