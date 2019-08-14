<?php

Route::get('/', 'Admin\OrganizationController@admin')->name('home');
Route::resource('/organization', 'Admin\OrganizationController')->except(['show']);
Route::get('/entry', 'Admin\EntryController@index')->name('entry.index');
Route::resource('/cp', 'Admin\CPController')->except(['create', 'store', 'show']);
Route::resource('/sp', 'Admin\SPController')->only(['index', 'destroy']);
Route::resource('/user', 'Admin\UserController');
Route::resource('/exchange', 'Admin\ExchangeController')->only(['index', 'show']);
Route::get('/exchange/{exchange}/regionRight/{regionRight}', 'Admin\ExchangeController@regionRight')->name('exchange.regionRight');
Route::put('/exchange/{exchange}/approve', 'Admin\ExchangeController@approve')->name('exchange.approve');
Route::put('/exchange/{exchange}/reject', 'Admin\ExchangeController@reject')->name('exchange.reject');
Route::get('/cp/api/{id}', 'Admin\CPController@api')->name('cp.api');
Route::get('/sp/{property}/api', 'Admin\SPController@api')->name('sp.api');
Route::get('/logs/node1', 'Admin\LogsController@index')->name('logs.node1');
Route::get('/logs/node2', 'Admin\LogsController@index')->name('logs.node2');
