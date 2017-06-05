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

Route::get('/','FeedController@home');
Route::get('/jf2feed','FeedController@home_jf2');
Route::get('category/{name}', 'FeedController@category');

Route::get('s/{eid}','PostController@shortener');

//NOTE: patterns for type, year, month, day, and daycount are in RouteServiceProvider
Route::get('{year}','FeedController@yearFeed');
Route::get('{year}/{month}','FeedController@monthFeed');
Route::get('{type_any}/{year}/{month}/{day}/{daycount}','PostController@view')->name('single_post_no_slug');
Route::get('{type_any}/{year}/{month}/{day}/{daycount}/{slug}','PostController@view')->name('single_post');
Route::get('{type}','FeedController@typeFeed');
Route::get('{type_i}','FeedController@typeFeedRedir');
