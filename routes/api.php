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
    Route::get('/test', function (){echo 66;});
    Route::get('/articles', 'Articles_Commen@get_articles_index');

});
Route::post('/articles/search', 'Articles_Commen@get_articles_search');
Route::post('/users/search', 'UsersCommon@get_users_search');
