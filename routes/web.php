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

Route::get('/', 'IndexController@index')->name('home');
Route::get('/home', 'IndexController@home');

Route::get('/login', 'LoginController@login')->name('login');
Route::get('/authorize', 'LoginController@callback');
Route::get('/logout', 'LoginController@logout')->name('logout');

Route::get('/user', 'ProfileController@user')->name('user');

Route::get('/home', 'PostController@myPost')->name('myPost');
Route::post('/posts/create', 'PostController@postCreate')->name('posts-create');
Route::post('/posts/like', 'PostController@addLike')->name('posts-like');
Route::get('/comments', 'PostController@comments')->name('comments');
Route::get('/sortByNameAsc', 'PostController@sortByNameAsc')->name('sortByNameAsc');
Route::get('/sortByLikeHighest', 'PostController@sortByLikeHighest')->name('sortByLikeHighest');
Route::get('/jsonView', 'PostController@jsonView')->name('jsonView');
Route::get('/downloadTxt', 'PostController@downloadTxt')->name('downloadTxt');



