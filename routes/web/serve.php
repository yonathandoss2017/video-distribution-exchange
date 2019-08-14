<?php

/**
 * Universal Image Controller for Video Thumbnails
 * Use $entry->updated_at as timestamp. It's for version controlling the cache.
 */
Route::get('/image/{prop_id}/video/{entry}/{timestamp}', 'Serve\ImageController@video')
    ->where(['prop_id' => '[0-9]+', 'entry' => '[0-9]+', 'timestamp' => '[0-9]+'])
    ->name('image.video');

/*
 * Universal Image Controller for Playlist Thumbnails
 * Use $playlist->updated_at as timestamp. It's for version controlling the cache.
 */
Route::get('/image/{prop_id}/playlist/{playlist}/{timestamp}', 'Serve\ImageController@playlist')
    ->where(['prop_id' => '[0-9]+', 'playlist' => '[0-9]+', 'timestamp' => '[0-9]+'])
    ->name('image.playlist');

/*
 * Universal Image Controller for Scenes Thumbnails
 * Use $entry_scenes->updated_at as timestamp. It's for version controlling the cache.
 */
Route::get('/image/scenes/{scenes}/{timestamp}', 'Serve\ImageController@scenes')
    ->where(['scenes' => '[0-9]+', 'timestamp' => '[0-9]+'])
    ->name('image.scenes');

/*
 * Universal Image Controller for property Thumbnails
 * Use $property->updated_at as timestamp. It's for version controlling the cache.
 */
Route::get('/image/property/{property}/{imagetype}/{timestamp}', 'Serve\ImageController@property')
    ->where(['property' => '[0-9]+', 'timestamp' => '[0-9]+'])
    ->name('image.property');

/*
 * Universal Image Controller for AI Review Result Thumbnails
 * Use $aiReviewVideoResult->updated_at as timestamp. It's for version controlling the cache.
 */
Route::get('/image/ai-review/result/{aiReviewVideoResult}/{timestamp}', 'Serve\ImageController@aiReviewResult')
    ->where(['result' => '[0-9]+', 'timestamp' => '[0-9]+'])
    ->name('image.ai-review.result');

/*
 * Testing
 */
Route::get('/', function () {
    $content = '<img src="/serve/image/3000061/video/5121/123">';

    return response($content);
});
