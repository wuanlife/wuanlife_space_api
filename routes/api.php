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
    'middleware' => [
        'logged',
    ]
], function () {
    Route::get('/test', 'TestAccessToken@token');
});
Route::post('/articles/{article_id}/approval','ArticleController@approval')->where('article_id','[0-9]+');
Route::delete('/articles/{article_id}/approval','ArticleController@del_approval')->where('article_id','[0-9]+');
Route::put('/users/{user_id}/collections','UserController@collect')->where('user_id','[0-9]+');
Route::delete('/users/{user_id}/collections','UserController@del_collect')->where('user_id','[0-9]+');