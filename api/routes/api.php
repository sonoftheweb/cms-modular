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

Route::get('app', 'AppController@app');
Route::resource('products', 'Api\ProductController')->only(['index']);

Route::group(['prefix' => 'auth'], function () {
    Route::post('registration', 'Auth\AuthenticationController@registerInstance');
    Route::post('login', 'Auth\AuthenticationController@login');
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::resource('payment', 'PaymentController');
});

Route::group(['middleware' => ['auth:api', 'protected_api']], function () {
    Route::post('auth/logout', 'Auth\AuthenticationController@logout');
    Route::resource('profile', 'Api\ProfileController')->only(['update']);

    Route::resource('users', 'UserController')->only([
        'index', 'store', 'show', 'update', 'destroy'
    ]);
    Route::get('me', 'UserController@me');
    Route::get('roles', 'UserController@roles');
});

Route::fallback(function(){
    return response()->json([
        'message' => 'No such resource...'], 404);
});
