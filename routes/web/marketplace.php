<?php

Route::group(['prefix' => 'api', 'as' => 'api.', 'namespace' => 'Marketplace\V1'], function () {
    Route::group(['prefix' => 'property'], function () {
        Route::get('/{id}', 'MarketplaceController@getProperty')->name('property.show');
        Route::get('/', 'MarketplaceController@propertyList')->name('property.index');
        Route::post('{property}/subscribe', 'MarketplaceController@subscribe')->name('property.subscribe');
        Route::post('{property}/unsubscribe', 'MarketplaceController@unsubscribe')->name('property.unsubscribe');
        Route::get('/sp/manage', 'MarketplaceController@SpList')->name('sp.list');
    });

    Route::get('subscription', 'MarketplaceController@subscription')->name('user.subscription.index');

    Route::get('/entry/{entry}/player', 'MarketplaceController@player')->name('entry.player');
    Route::get('/entry/{entry}', 'MarketplaceController@entryShow')->name('entry.show');
    Route::get('/entry', 'MarketplaceController@entryIndex')->name('entry.index');

    Route::get('/playlist', 'MarketplaceController@playlistIndex')->name('playlist.index');
    Route::get('/playlist/{playlist}', 'MarketplaceController@playlist')->name('playlist.show');

    Route::group(['prefix' => 'cart', 'as' => 'cart'], function () {
        Route::get('/', 'MarketplaceController@cart');
        Route::post('/add', 'MarketplaceController@cartAdd')->name('.add');
        Route::delete('/remove', 'MarketplaceController@cartRemove')->name('.remove');
        Route::post('/checkout', 'MarketplaceController@cartCheckout')->name('.checkout');
    });

    Route::get('/set-locale/{locale}', 'MarketplaceController@setLocale')->name('set-locale');

    Route::get('/query', 'MarketplaceController@query')->name('query');
});

Route::get('/', ['as' => 'index', 'uses' => 'Marketplace\MarketplaceController@index']);
Route::get('/{any}', ['uses' => 'Marketplace\MarketplaceController@index'])->where('any', '.*');
