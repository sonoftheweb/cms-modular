<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('registration', 'Auth\AuthenticationController@registerInstance');
    Route::post('login', 'Auth\AuthenticationController@login');
    Route::get('test', function () {
        dd(explode(',', env('SANCTUM_STATEFUL_DOMAINS')));
    });
});

Route::group(['prefix' => 'payment', 'middleware' => ['auth:api', 'response_in_request']], function () {
    Route::get('plans', 'PaymentController@getPlans');
    Route::get('payment_methods', 'PaymentController@getPaymentMethod');
    Route::get('intent', 'PaymentController@getIntent');
    Route::post('savePaymentMethod', 'PaymentController@savePaymentMethod');
    Route::get('paymentIntent', 'PaymentController@paymentIntent');
    Route::post('subscribe', 'PaymentController@subscribe');
});

Route::group(['middleware' => ['auth:api', 'protected_api']], function () {
    Route::post('auth/logout', 'Auth\AuthenticationController@logout');
    Route::get('me', 'UserController@me');
});

Route::fallback(function(){
    return response()->json([
        'message' => 'No such resource...'], 404);
});
