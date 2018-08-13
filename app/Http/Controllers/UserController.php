<?php

namespace App\Http\Controllers;

use App\Models\Articles\Articles_status;
use Illuminate\Http\Request;
use App\Models\Users\user_collection;
use App\Models\Articles\Article;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    //收藏文章A12
    public function collect($user_id, Request $request)
    {
        $uid = $request->get('Access-Token')->uid;
        //$uid = 2;
        if (isset($uid)) {
            //判断是否登陆
            if ($uid == $user_id) {
                //判断uid和token里的id是否一致
                $article_id = $request->input("artilce_id");
                //$article_id = 1;
                $bool = Article::find($article_id);
                if (isset($bool)) {
                    //判断文章是否存在
                    $status = Articles_status::where('id', $article_id)->first()->status;
                    if ($status != 4) {
                        //判断文章是否被删除 4为删除
                        $user = User_collection::where(['user_id' => $user_id, 'article_id' => $article_id])->first();
                        if (!isset($user)) {
                            //判断是否被收藏
                            $a = new User_collection;
                            $a->user_id = $user_id;
                            $a->article_id = $article_id;
                            $bool = $a->save();
                            if ($bool == true) {
                                return response(['收藏成功'], 204);
                            } else {
                                return response(['收藏失败'], 400);
                            }
                        } else {
                            return response(['已收藏'], 204);
                        }
                    } else {
                        return response(['文章已被删除'], 410);
                    }
                } else {
                    return response(['文章不存在'], 404);
                }
            } else {
                return response(['没有权限操作'], 403);
            }
        } else {
            return response(['未登录，不能操作'], 401);
        }

    }

    //取消收藏文章A16
    public function del_collect($user_id, Request $request)
    {
        $uid = $request->get('Access-Token')->uid;
        //$uid = 2;
        if (isset($uid)) {
            //判断是否登陆
            if ($uid == $user_id) {
                //判断uid和token里的id是否一致
                $article_id = $request->input("artilce_id");
                //$article_id = 1;
                $bool = Article::find($article_id);
                if (isset($bool)) {
                    //判断文章是否存在
                    $status = Articles_status::where('id', $article_id)->first()->status;
                    if ($status != 4) {
                        //判断文章是否被删除 4为删除
                        $user = User_collection::where(['user_id' => $user_id, 'article_id' => $article_id])->first();
                        if (isset($user)) {
                            //判断是否被收藏
                            $bool = User_collection::where(['user_id' => $user_id, 'article_id' => $article_id])->delete();
                            if ($bool == true) {
                                return response(['取消收藏成功'], 204);
                            } else {
                                return response(['取消收藏成功失败'], 400);
                            }
                        } else {
                            return response(['已取消收藏'], 204);
                        }
                    } else {
                        return response(['文章已被删除'], 410);
                    }
                } else {
                    return response(['文章不存在'], 404);
                }
            } else {
                return response(['没有权限操作'], 403);
            }
        } else {
            return response(['未登录，不能操作'], 401);
        }
    }
}
