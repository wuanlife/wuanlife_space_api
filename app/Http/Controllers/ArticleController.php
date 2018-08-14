<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articles\Article_approval;

class ArticleController extends Controller
{
    // A2 点赞
    // articles_approval 表中记录点赞状态
    public function approval($article_id, Request $request)
    {
        if ($request->get('Access-Token') != NULL) {
            $user_id = $request->get('Access-Token')->uid;
            $user = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
            if (!isset($user)) {
                $a = new Article_Approval;
                $a->article_id = $article_id;
                $a->user_id = $user_id;
                $bool = $a->save();
                if ($bool == true) {
                    return response(['点赞成功'], 204);
                } else {
                    return response(['点赞失败'], 400);
                }
            } else {
                return response(['点赞失败'], 204);
            }
        } else {
            return response(['未登录，不能操作'], 401);
        }
    }

//A15 取消点赞
    public function del_approval($article_id, Request $request)
    {
        if ($request->get('Access-Token') != NULL) {
            $user_id = $request->get('Access-Token')->uid;
            $bool = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->delete();
            if ($bool == true) {
                return response(['取消点赞成功'], 204);
            } else {
                return response(['取消点赞失败'], 400);
            }
        } else {
            return response(['未登录，不能操作'], 401);
        }
    }
}
