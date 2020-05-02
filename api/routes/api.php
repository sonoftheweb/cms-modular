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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('{resource}', 'Api\ApiController@getCollection');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::fallback(function(){
    return response()->json([
        'message' => 'No such resource...'], 404);
});
