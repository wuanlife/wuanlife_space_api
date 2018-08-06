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
 * 不需要权限验证的接口
 *****************************************/
Route::group([

], function () {
    // A5 文章评论列表
    Route::get('/articles/{id}/comments', 'Articles_Commen@get_comments_list');
});

/*****************************************
 * 需要登录后操作的接口
 *****************************************/
Route::group([
    'middleware' => [
        'logged',
    ]
], function () {
    Route::post('/articles/{id}/comments', 'Articles_Commen@add_comments');
    Route::delete('/articles/{id}/comments/{floor}', 'Articles_Commen@delete_comments');
});

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

