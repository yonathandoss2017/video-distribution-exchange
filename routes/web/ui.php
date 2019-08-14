<?php

Route::get('/', function () {
    return 'ui-only';
});

Route::view('/ivideostream', 'ui.ivideostream');

Route::view('/oss-uploader', 'ui.oss-uploader');

Route::view('/request-logs', 'ui.request-logs');

Route::view('/cp/analytics', 'ui.cp.analytics');

Route::view('/sp/analytics', 'ui.sp.analytics');

Route::view('/sp/syndication', 'ui.sp.syndication');

Route::view('/marketplace/cp', 'marketplace.v3.cp-detail');

Route::view('/request-logs/playlist-show', 'ui.cp.playlist_show');

Route::view('/request-logs/edit-comments', 'ui.cp.request-log-edit-comments');

Route::group(['middleware' => 'auth'], function () {
    Route::view('/error/ivx-admin', 'ui.error.ivx_admin_error');
    Route::view('/error/dashboard', 'ui.error.dashboard_error');
});

Route::view('/cp/block-chain/create', 'ui.cp.block-chain.create');

Route::view('/cp/block-chain/create-review', 'ui.cp.block-chain.create-review');

Route::view('/cp/video/create', 'ui.cp.video.create');

Route::view('/cp/video/edit', 'ui.cp.video.edit');

Route::view('/cp/video', 'ui.cp.video.index');

Route::view('/sp/video', 'ui.sp.video.index');

Route::view('/cp/tod/create', 'ui.cp.tod.create');

Route::view('/organization/content/video', 'ui.organization.content.video');

Route::view('/organization/content/playlist', 'ui.organization.content.playlist');

Route::view('/organization/content/publish-request', 'ui.organization.content.publish-request');

Route::view('/cp/exchange/whitelist', 'ui.cp.exchange.whitelist');

Route::view('/cp/exchange/whitelist/sp', 'ui.cp.exchange.whitelist-sp');
