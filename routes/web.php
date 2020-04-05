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
Route::get('/home', 'IndexController@index');
Route::get('/dashboard', 'IndexController@dashboard')->name('dashboard');

Route::get('/login', 'LoginController@login')->name('login');
Route::get('/authorize', 'LoginController@callback');
Route::get('/logout', 'LoginController@logout')->name('logout');

Route::get('/user', 'ProfileController@user')->name('user');

Route::get('/posts', 'PostController@posts')->name('posts');
Route::get('/showAllPosts', 'PostController@showAllPosts')->name('showAllPosts');
Route::post('/posts/create', 'PostController@postCreate')->name('posts-create');
Route::post('/posts/like', 'PostController@addLike')->name('posts-like');
//Route::post('/posts/delete', 'PostController@postDelete')->name('posts-delete');

