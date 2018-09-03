<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articles\Article_approval;
use App\Models\Articles\Article_base;
use App\Models\Articles\Articles_status;

class ArticleController extends Controller
{
    // A2 点赞
    // articles_approval 表中记录点赞状态
    public function approval($article_id, Request $request)
    {
        //判断用户是否登陆
        if ($request->get('Access-Token') == NULL) {
            return response(['未登录，不能操作'], 401);
        }

        //判断文章是否存在
        $user_id = $request->get('Access-Token')->uid;
        $bool = Article_base::find($article_id);
        if (!isset($bool)) {
            return response(['文章不存在'], 404);
        }

        //判断文章是否被删除 4为删除
        $status = Articles_status::where('id', $article_id)->first()->status;
        if ($status == 4) {
            return response(['文章已被删除'], 410);
        }

        //判断用户是否已点赞
        $user = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
        if (isset($user)) {
            return response(['已点赞'], 204);
        }

        //点赞
        $a = new Article_Approval;
        $a->article_id = $article_id;
        $a->user_id = $user_id;
        $bool = $a->save();
        if ($bool == true) {
            return response(['点赞成功'], 200);
        } else {
            return response(['点赞失败'], 400);
        }
    }

//A15 取消点赞
    public function del_approval($article_id, Request $request)
    {
        //判断用户是否登陆
        if ($request->get('Access-Token') == NULL) {
            return response(['未登录，不能操作'], 401);
        }

        //判断文章是否存在
        $user_id = $request->get('Access-Token')->uid;
        $bool = Article_base::find($article_id);
        if (!isset($bool)) {
            return response(['文章不存在'], 404);
        }

        //判断文章是否被删除 4为删除
        $status = Articles_status::where('id', $article_id)->first()->status;
        if ($status == 4) {
            return response(['文章已被删除'], 410);
        }

        //判断用户是否已点赞
        $user = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
        if (isset($user)) {
            return response(['已点赞'], 204);
        }

        //取消点赞
        $bool = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->delete();
        if ($bool == true) {
            return response(['取消点赞成功'], 204);
        } else {
            return response(['取消点赞失败'], 400);
        }
    }
}
