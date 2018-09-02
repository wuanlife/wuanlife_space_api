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
    Route::get('/articles/{id}/comments', 'ArticlesCommentsController@index');
    // A1 首页
    Route::get('/articles', 'ArticlesController@index');
    // A14 文章搜索
    Route::post('/articles/search', 'Articles_Commen@get_articles_search');
    // U6 用户搜索
    Route::post('/users/search', 'UsersCommon@get_users_search');
    // A3 获取用户文章列表
    Route::get('/users/{id?}/articles',['uses'=>'ArticlesController@getUsersArticles']);
    // A4 文章详情
    Route::get('/articles/{id?}',['uses'=>'ArticlesController@show']);
});

/*****************************************
 * 需要登录后操作的接口
 *****************************************/
Route::group([
    'middleware' => [
        'logged',
    ]
], function () {
    // A2 点赞文章
    Route::post('/articles/{id}/approval', 'ArticlesController@approval');
    // A6 发表文章
    Route::post('/articles', 'ArticlesController@create');
    // A8 编辑文章
    Route::put('/articles/{id}',['uses'=>'ArticlesController@putArticles']);
    // A11 删除文章
    Route::delete('/articles/{id}',['uses'=>'ArticlesController@deleteArticles']);
    // A7 评论文章
    Route::post('/articles/{id}/comments', 'Articles_Commen@add_comments');
    // A9 删除文章评论
    Route::delete('/articles/{id}/comments/{floor}', 'ArticlesCommentsController@delete');
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
    Route::post('/articles/{id}/lock', 'ArticlesStatusController@lock');
    //  A17 取消锁定
    Route::post('/articles/{id}/unlock', 'ArticlesStatusController@unlock');
});
Route::post('/articles/{article_id}/approval','ArticleController@approval')->where('article_id','[0-9]+');
Route::delete('/articles/{article_id}/approval','ArticleController@del_approval')->where('article_id','[0-9]+');
Route::put('/users/{user_id}/collections','UserController@collect')->where('user_id','[0-9]+');
Route::delete('/users/{user_id}/collections','UserController@del_collect')->where('user_id','[0-9]+');