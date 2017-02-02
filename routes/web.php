<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
    // return view('welcome');
// });

Route::get('/','PostController@home');
Route::get('category/{name}', 'PostController@category');
Route::get('{type}','PostController@typeFeed');
Route::get('{year}/{month}','PostController@monthFeed')->where('year' => '[0-9]+', 'month' => '[0-9]+');
Route::get('{type}/{year}/{month}/{day}/{daycount}','PostController@view');
Route::get('{type}/{year}/{month}/{day}/{daycount}/{slug}','PostController@view');
