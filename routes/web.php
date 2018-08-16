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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users/{id?}/articles',['uses'=>'ArticlesController@getUsersArticles']);
Route::get('/articles/{id?}',['uses'=>'ArticlesController@getArticles']);
Route::post('/articles',['uses'=>'ArticlesController@postArticles']);
Route::put('/articles/{id?}',['uses'=>'ArticlesController@putArticles']);
Route::delete('/articles/{id}',['uses'=>'ArticlesController@deleteArticles']);
