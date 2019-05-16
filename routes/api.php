<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::get('register/activate/{token}', 'AuthController@signupActivate');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@getData');
        Route::delete('/{id}', 'AuthController@destroy');
    });
});
Route::group([
    'prefix' => 'job'
], function () {
    Route::get('/', 'JobController@index');
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::post('/', 'JobController@store');
        Route::put('/{id}', 'JobController@update');
        Route::delete('/{id}', 'JobController@destroy');

    });
});
