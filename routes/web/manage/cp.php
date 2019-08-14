<?php
/*
|--------------------------------------------------------------------------
| CP Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| w.r.t CP.
|
 */

Route::group(['middleware' => ['check_cp', 'can:manage-property,property'], 'prefix' => '/{property}/cp', 'as' => 'manage.cp.', 'namespace' => 'Manage\CP'], function () {
    // Manage > CP > Playlists
    Route::get('/playlists/{playlist}/publish', 'PlaylistController@publish')->name('playlists.publish');
    Route::put('/playlists/{playlist}/publish', 'PlaylistController@updatePublish')->name('playlists.update-publish');
    Route::resource('/playlists', 'PlaylistController', ['except' => 'show']);
    Route::get('/playlists/get_playlists', 'PlaylistController@getPlaylists')->name('playlists.get_playlist');
    if (config('features.content_review')) {
        Route::post('/playlists/review/request', 'PlaylistController@requestReview')->name('playlists.review.request');
    }

    // Manage > CP > Videos
    Route::get('/upload-videos', 'UploadVideoController@upload')->name('upload.videos');
    Route::group(['prefix' => '/videos'], function () {
        Route::get('/', 'VideoController@index')->name('videos');
        Route::get('/add', 'UploadVideoController@index')->name('add.video');
        Route::post('/add', 'UploadVideoController@store')->name('store.video');
        Route::group(['as' => 'video.'], function () {
            Route::get('/video/{id}/player', 'VideoController@playerShow')->name('player');
            Route::get('/edit/{id}', 'UploadVideoController@edit')->name('edit');
            Route::get('{id}/download', 'UploadVideoController@download')->name('download');
            Route::post('/edit/{id}', 'UploadVideoController@update')->name('update');
            Route::delete('/delete/bulk', 'VideoController@bulkDelete')->name('delete.bulk');
            Route::delete('/delete/{id}', 'VideoController@delete')->name('delete');
            Route::post('/export/bulk', 'VideoController@bulkExport')->name('export.bulk');
            Route::post('/import/bulk', 'VideoController@bulkImport')->name('import.bulk');
            Route::post('/review/bulk', 'VideoController@requestReview')->name('review.bulk');
            Route::post('/store-video', 'UploadVideoController@storeVideo')->name('store-video');
            Route::post('/{id}/analyze', 'VideoController@analyze')->name('analyze');
            Route::get('/{id}/results', 'VideoController@resultAnalyze')->name('analyze.results');
            Route::get('/{id}/results/time-frames', 'VideoController@getTimeFrames')->name('analyze.results.getTimeFrames');
        });
        Route::delete('/delete-object', 'UploadVideoController@deleteObject');
        Route::get('/request_upload', 'UploadVideoController@requestUpload')->name('request-upload');
    });

    //Manage > CP > Request Log
    Route::group(['prefix' => '/request-logs', 'as' => 'request-logs.'], function () {
        Route::get('/', 'RequestLogController@index')->name('index');
        Route::post('/{id}/approve', 'RequestLogController@approve')->name('approve');
        Route::post('/{id}/reject', 'RequestLogController@reject')->name('reject');
        Route::post('/{id}/ai-review', 'RequestLogController@aiReview')->name('ai-review');
        Route::get('/videos/{id}', 'RequestLogController@show')->name('video.show');
        Route::get('/playlist/{id}', 'RequestLogController@showPlaylist')->name('playlist.show');
        Route::get('/comment/edit/{id}/{type}', 'RequestLogController@commentEdit')->name('comment.edit');
        Route::put('/comment/{id}/{type}', 'RequestLogController@commentStore')->name('comment.store');
        Route::post('/approve/bulk', 'RequestLogController@bulkApprove')->name('approve.bulk');
        Route::post('/reject/bulk', 'RequestLogController@bulkReject')->name('reject.bulk');
        Route::post('/ai-review/bulk', 'RequestLogController@bulkAiReview')->name('ai-review.bulk');
        Route::get('/{id}/ai-review/result', 'RequestLogController@aiReviewResult')->name('ai-review.result');
    });

    // Manage > CP > OAuth
    Route::group(['prefix' => '/oauths', 'as' => 'oauths.'], function () {
        Route::get('/', 'PlatformController@index')->name('index');
        Route::get('/add/alioss', 'PlatformController@addAliOss')->name('add.alioss');
        Route::post('/store/alioss', 'PlatformController@storeAliOss')->name('store.alioss');
        Route::get('/alioss/{id}', 'PlatformController@showAliOss')->name('show.alioss');
        Route::put('/update/alioss/{id}', 'PlatformController@updateAliOss')->name('update.alioss');
        Route::delete('/delete/alioss/{id}', 'PlatformController@deleteAliOss')->name('delete.alioss');
    });

    // Manage > CP > DPP
    Route::group(['middleware' => 'can:manage-dpp,property'], function () {
        Route::group(['prefix' => '/dpp', 'as' => 'dpp.'], function () {
            Route::get('/{playlist}/video/list', 'DppController@getVideoList')->name('playlist.videos');
            Route::post('/videos/select', 'DppController@selectVideo')->name('videos.select');
            Route::get('/create/review', 'DppController@createReview')->name('create.review');
            Route::delete('/{playlist_id}/video/{video_id}', 'DppController@deleteEntry')->name('entry.destroy');
            Route::resource('/{playlist_id}/video/{video_id}/scenes', 'DppSceneController', ['except' => ['create', 'store']]);
        });
        Route::resource('/dpp', 'DppController');
    });

    //Manage > CP > Exchange
    Route::group(['middleware' => ['can:manage-exchange,property'], 'prefix' => '/exchange', 'as' => 'exchange.', 'namespace' => 'Exchange'], function () {
        Route::resource('/distribution', 'DistributionController');
        Route::group(['prefix' => '/distribution', 'as' => 'distribution.'], function () {
            Route::post('/{distribution}/duplicate', 'DistributionController@duplicate')->name('duplicate');
            Route::put('/{distribution}/revert_to_draft', 'DistributionController@revert_to_draft')->name('revert_to_draft');
            Route::put('/{distribution}/revoke', 'DistributionController@revoke')->name('revoke');
            Route::put('/{distribution}/publish', 'DistributionController@publish')->name('publish');
            Route::get('/summary/create', 'DistributionController@summary_create')->name('summary.create');
            Route::post('/summary/store', 'DistributionController@summary_store')->name('summary.store');
            Route::get('/{distribution}/summary/edit', 'DistributionController@summary_edit')->name('summary.edit');
            Route::put('/{distribution}/summary/update', 'DistributionController@summary_update')->name('summary.update');
            Route::get('/{distribution}/sp/index', 'DistributionController@sp_index')->name('sp.index');
            Route::get('/{distribution}/sp/list', 'DistributionController@sp_list')->name('sp.list');
            Route::get('/{distribution}/sp/search', 'DistributionController@sp_search')->name('sp.search');
            Route::get('/{distribution}/sp/select/{sp_id}', 'DistributionController@sp_select')->name('sp.select');
            Route::put('/{distribution}/sp/select', 'DistributionController@sp_store')->name('sp.store');
            Route::delete('/{distribution}/sp/delete', 'DistributionController@sp_delete')->name('sp.delete');
            Route::get('/{id}/regions/marketplace-term', 'DistributionRegionsController@marketplaceTerm')->name('regions.marketplace_term');
            Route::resource('/{id}/regions', 'DistributionRegionsController', ['except' => ['index']]);
            Route::put('/{distribution}/own_sp/confirm', 'DistributionController@confirm_own_sp')->name('own_sp.confirm');
        });
        Route::resource('marketplace-terms', 'MarketplaceTermsController');
        Route::group(['prefix' => '/request-logs', 'as' => 'request_logs.'], function () {
            Route::get('/', 'RequestLogController@index')->name('index');
            Route::get('/{requestLog}', 'RequestLogController@show')->name('show');
        });

        Route::group(['prefix' => 'notification-settings', 'as' => 'notification-settings.'], function () {
            Route::get('/', 'NotificationSettingController@index')->name('index');
            Route::put('/{user}', 'NotificationSettingController@update')->name('update');
        });
    });

    Route::group(['prefix' => '/licensing', 'as' => 'notifications'], function () {
        Route::get('/notifications', 'NotificationController@notifications');
        Route::post('/notifications/enable', 'NotificationController@enable_notifications')->name('.enable');
        Route::post('/notifications/disable', 'NotificationController@disable_notifications')->name('.disable');
    });

    // Manage > CP > Settings
    Route::group(['middleware' => ['can:manage-setting,property'], 'prefix' => '/settings'], function () {
        Route::get('/', 'PropertyController@propertySettings')->name('settings');
        Route::post('/', 'PropertyController@updateProperty')->name('update');
    });

    // Manage > CP > distribution
    Route::get('/distributions/{termsOfDistribution}/playlists', 'DistributionPlaylistController@index')->name('distribution.playlist');
    Route::get('/distributions/{termsOfDistribution}/playlists/ajax/query', 'DistributionPlaylistController@jsonPlaylist');
    Route::put('/distributions/{termsOfDistribution}/playlists', 'DistributionPlaylistController@update');

    // Manage > CP > Analytics
    Route::group(['prefix' => '/analytics', 'as' => 'analytics.'], function () {
        Route::get('/', 'AnalyticsController@index')->name('index');
    });

    Route::group(['prefix' => '/block-chain', 'as' => 'block-chain.', 'namespace' => '\BlockChain', 'middleware' => 'can:manage-block-chain,property'], function () {
        Route::get('/', 'EvidenceControler@index')->name('index');
        Route::get('/create', 'EvidenceControler@create')->name('create');
        Route::get('/{playlist}/video/list', 'EvidenceControler@getVideoList')->name('playlist.videos');
        Route::post('/videos/select', 'EvidenceControler@selectVideo')->name('videos.select');
        Route::get('/create/review', 'EvidenceControler@createReview')->name('create.review');
        Route::post('/store', 'EvidenceControler@store')->name('store');
        Route::get('/{id}/edit', 'EvidenceControler@edit')->name('edit');
        Route::get('/get-receipt/{entry_id}', 'EvidenceControler@getReceipt')->name('get-receipt');

        Route::resource('/video-compare', 'VideoCompareController')->only(['index', 'create', 'store', 'show']);
    });
});
