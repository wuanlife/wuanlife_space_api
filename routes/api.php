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

/*****************************************
 * 需要登录后操作的接口
 *****************************************/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => [
        'logged',
    ]
], function () {
    Route::get('/articles', 'Articles_Commen@get_articles_index');

});
Route::post('/articles/search', 'Articles_Commen@get_articles_search');
Route::post('/users/search', 'UsersCommon@get_users_search');
/*****************************************
 * 需要管理员权限的接口
 *****************************************/
Route::group([
    'middleware' => [
        'logged',
        'admin',
    ]
], function () {
    // A10 锁定文章
    Route::post('/articles/{id}/lock', 'ArticlesController@lock');
    //  A17 取消锁定
    Route::post('/articles/{id}/unlock', 'ArticlesController@unlock');


});
Route::post('/articles/{article_id}/approval','ArticleController@approval')->where('article_id','[0-9]+');
Route::delete('/articles/{article_id}/approval','ArticleController@del_approval')->where('article_id','[0-9]+');
Route::put('/users/{user_id}/collections','UserController@collect')->where('user_id','[0-9]+');
Route::delete('/users/{user_id}/collections','UserController@del_collect')->where('user_id','[0-9]+');