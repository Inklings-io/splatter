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

//Route::get('/user', function (Request $request) {
    //return $request->user();
//})->middleware('auth:api');

Route::post('webmention','WebmentionController@index');

Route::post('token','TokenController@index');

Route::get('micropub','MicropubController@get_index')->middleware('verify_token');
Route::post('micropub','MicropubController@post_index')->middleware('verify_token');

Route::post('media','MediaController@index')->middleware('verify_token');

