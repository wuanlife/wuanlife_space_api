<?php

namespace App\Http\Controllers;

use App\Models\Articles\ArticlesBase;
use App\Models\Articles\ArticlesStatusDetail;
use App\Models\Articles\ArticlesContent;
use App\Models\Articles\ArticlesApproval;
use App\Models\Articles\ArticlesApprovalCount;
use App\Models\Articles\UsersArticlesCount;
use App\Models\Articles\Users_Base;
use App\Models\Articles\Articles_Comments;
use App\Models\Articles\Articles_Comments_Count;
use App\Models\Articles\ImageUrl;
use App\Models\Users\UserCollections;
use App\Models\Users\AvatarUrl;


class ArticlesController extends Controller
{
    public function lock($id)
    {
        $article = ArticlesBase::find($id);
        if (!$article) {
            return response(['error' => '该文章不存在'], 404);
        }

        if (!$article->status) {
            $status = $article->status()->create([
                'status' => (1 << 1)
            ]);

            if ($status->id) {
                return response([], 204);
            } else {
                return response(['error' => '锁定失败'], 400);
            }
        }

        if (isset($article->status->status)) {
            $status = $article->status()->update([
                'status' => $article->status->status | (1 << 1)
            ]);
            if (false !== $status) {
                return response([], 204);
            } else {
                return response(['error' => '锁定失败'], 400);
            }
        }


        if (($article->status->status & (1 << ArticlesStatusDetail::where('detail', '删除')->value('status')))) {
            return response(['error' => '该文章已被删除'], 410);
        }

        if ($article->status->status & (1 << ArticlesStatusDetail::where('detail', '锁定')->value('status'))) {
            $this->response(['error' => '该文章已被锁定！'], 400);
        }
    }

    public function unlock($id)
    {
        $article = ArticlesBase::find($id);
        if (!$article) {
            return response(['error' => '该文章不存在'], 404);
        }


        if (($article->status->status & (1 << ArticlesStatusDetail::where('detail', '删除')->value('status')))) {
            return response(['error' => '文章已被删除'], 410);
        }

        $status = $article->status()->update([
            'status' => ($article->status->status & (~(1 << 1)))
        ]);

        if (false !== $status) {
            return response([], 204);
        } else {
            return response(['error' => '取消锁定失败'], 400);
        }
    }

    /**
     * 获取用户文章列表 (A3)
     * GET /users/:id/articles
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersArticles($id=NULL)
    {
        //如果无用户id返回空
        if(empty($id)){
            return response('',200);
        }
        //用户、作者相关
        $res_author = Users_Base::getUserInfo($id);
        if(is_null($res_author)){
            return response(['error'=>'获取用户文章列表失败'],400);
        }
        $author['avatar_url'] = AvatarUrl::getUrl($id);
        $author['name'] = $res_author['name'];
        $author['id'] = $id;
        $author['articles_num'] = UsersArticlesCount::ArticlesNum($id);
        //文章相关
        $res_articlebase = ArticlesBase::getUsersArticles($id);
        if(empty($res_articlebase)){
            return response(['articles' => array()],200);
        }
        foreach($res_articlebase as $key => $res){
            $articles[$key]['id'] = $res['id'];
            $articles[$key]['title'] = ArticlesContent::getArticleTitle($res['id']);
            $articles[$key]['content'] = ArticlesContent::getArticleContent($res['id']);
            $articles[$key]['update_at'] = $res['update_at'];
            $articles[$key]['create_at'] = $res['create_at'];
            $articles[$key]['approved'] = ArticlesApproval::getApproved($res['id']);
            $articles[$key]['approved_num'] = ArticlesApprovalCount::getApprovedNum($res['id']);
            $articles[$key]['collected'] = UserCollections::getIsCollected($id,$res['id']);
            $articles[$key]['collected_num'] = UserCollections::getCollectedNum($res['id']);
            $articles[$key]['replied'] = Articles_Comments::ArticleIsReplied($res['id']);
            $articles[$key]['replied_num'] = Articles_Comments_Count::getRepliedNum($res['id']);
            $articles[$key]['image_urls'] = ImageUrl::getImageUrls($res['id']);
        }
        $response['articles'] = $articles;
        $response['author'] = $author;
        return response()->json($response,200)->setEncodingOptions(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
