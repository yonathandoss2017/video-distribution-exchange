<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
 */

/*
 * Authentication.
 */
Route::get('/logout', 'Auth\LoginController@logout');
Auth::routes();
Route::get('password/reset/{url_encoded_email}/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::get('password/activate/{url_encoded_email}/{token}', 'Auth\ResetPasswordController@showActivateResetForm');
Route::get('activate_password/email/{email}', 'Auth\ForgotPasswordController@sendActivateResetLinkEmail');
Route::get('activate/{token}', 'Auth\ActivateUserController@activate');

/*
 * Oct 2, 2017
 * User not allowed to create account directly
 * Route::get('/signup', 'SignupController@create_bak');
 */
Route::get('/signup', 'Auth\SignupController@create');
Route::post('/signup_bak', 'Auth\SignupController@store');
Route::get('/signup_success', 'Auth\SignupController@signupSuccess');

/*
 * IVX setLocale
 */
Route::get('/set_locale/{locale}', 'HomeController@setLocale')->name('ivx.setLocale');

Route::get('/', function () {
    return view('landing');
});

Route::group(['middleware' => ['web', 'auth', 'can:super-admin'], 'prefix' => 'solr', 'namespace' => 'Solr'], function () {
    Route::get('/ping', 'SolariumController@ping');
    Route::get('/search', 'SolariumController@search');
});

Route::get('check-email', ['as' => 'check-email', 'uses' => 'Auth\CheckEmailController@action']);

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
