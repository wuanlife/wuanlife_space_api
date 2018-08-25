<?php

namespace App\Http\Controllers;

use App\Models\Articles\Articles_status;
use Illuminate\Http\Request;
use App\Models\Users\user_collection;
use App\Models\Articles\Article_base;

class UserController extends Controller
{
    //收藏文章A12
    public function collect($user_id, Request $request)
    {
        //判断是否登陆
        if ($request->get('Access-Token') == NULL) {
            return response(['未登录，不能操作'], 401);
        }

        //判断uid和token里的id是否一致
        $uid = $request->get('Access-Token')->uid;
        if ($uid != $user_id) {
            return response(['没有权限操作'], 403);
        }

        //判断文章是否存在
        $article_id = $request->input("artilce_id");
        $bool = Article_base::find($article_id);
        if (!isset($bool)) {
            return response(['文章不存在'], 404);
        }

        //判断文章是否被删除 4为删除
        $status = Articles_status::where('id', $article_id)->first()->status;
        if ($status == 4) {
            return response(['文章已被删除'], 410);
        }

        //判断是否被收藏
        $user = User_collection::where(['user_id' => $user_id, 'article_id' => $article_id])->first();
        if (isset($user)) {
            return response(['已收藏'], 204);
        }

        //收藏文章
        $a = new User_collection;
        $a->user_id = $user_id;
        $a->article_id = $article_id;
        $bool = $a->save();
        if ($bool == true) {
            return response(['收藏成功'], 204);
        } else {
            return response(['收藏失败'], 400);
        }
    }

    //取消收藏文章A16
    public function del_collect($user_id, Request $request)
    {

        //判断是否登陆
        if ($request->get('Access-Token') == NULL) {
            return response(['未登录，不能操作'], 401);
        }

        //判断uid和token里的id是否一致
        $uid = $request->get('Access-Token')->uid;
        if ($uid != $user_id) {
            return response(['没有权限操作'], 403);
        }

        //判断文章是否存在
        $article_id = $request->input("artilce_id");
        $bool = Article_base::find($article_id);
        if (!isset($bool)) {
            return response(['文章不存在'], 404);
        }

        //判断文章是否被删除 4为删除
        $status = Articles_status::where('id', $article_id)->first()->status;
        if ($status == 4) {
            return response(['文章已被删除'], 410);
        }

        //判断是否被收藏
        $user = User_collection::where(['user_id' => $user_id, 'article_id' => $article_id])->first();
        if (!isset($user)) {
            return response(['文章未收藏'], 204);
        }

        //取消收藏文章
        $bool = User_collection::where(['user_id' => $user_id, 'article_id' => $article_id])->delete();
        if ($bool == true) {
            return response(['取消收藏成功'], 204);
        } else {
            return response(['取消收藏成功失败'], 400);
        }
    }
}
