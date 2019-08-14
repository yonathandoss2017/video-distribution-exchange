<?php

/*
* All routes inside this file will not have API rate limit (defined in app/Http/kernel.php
* while all other api routes will keep use default API rate limit
*/

//Common Entry V2, we didn't use route grouping here for efficiency reason, so other v2 API will not be evaluated here.
Route::get('v2/common/entry', ['as' => 'v2.common.entry', 'uses' => 'Api\V2\Common\EntryController'])->middleware(['is_service_provider:true']);

//Notification from Ali MNS
Route::post('/ali/mns/events', 'Api\PlatformNotificationController@aliMnsEventsNotify');
Route::post('/ali/vod/notification', 'Api\PlatformNotificationController@aliVod');

//Notification from fingerprint server
Route::post('/fingerprint/extraction/notification', 'Api\BlockchainNotificationController@fingerprintExtraction')->name('fingerprint.extraction.notification');
Route::post('/fingerprint/compare/notification', 'Api\BlockchainNotificationController@fingerprintCompare')->name('fingerprint.compare.notification');
