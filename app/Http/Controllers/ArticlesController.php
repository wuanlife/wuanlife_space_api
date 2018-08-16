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
use App\Models\Articles\ArticlesStatus;
use App\Models\Users\UserCollections;
use App\Models\Users\AvatarUrl;
use Illuminate\Http\Request;


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
     * 获取用户文章列表(A3)
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

    /**
     * 文章详情-文章内容(A4)
     * GET /articles/:id
     * @param null $article_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getArticles($article_id=NULL)
    {
        //通过Access-Token获取用户是否登录
        $user_id = NULL;
        //地址中未传入article_id，无法查到对应文章详情
        if(is_null($article_id)){
            return response(['error' => '查看文章详情失败'],400);
        }
        //获取文章相关信息
        $res_articlebase = ArticlesBase::getArticleUser($article_id);
        if(empty($res_articlebase)){
            return response(['error' => '文章不存在'],404);
        }
        $res_articlebase = $res_articlebase[0];
        //查询文章状态是否为delete状态
        if(ArticlesStatus::is_status($article_id,'delete')){
            return response(['error' => '文章已被删除'],410);
        }
        $article['id'] = $article_id;
        $article['title'] = ArticlesContent::getArticleTitle($article_id);
        $article['content'] = ArticlesContent::getArticleContent($article_id);
        $article['update_at'] = $res_articlebase['update_at'];
        $article['create_at'] = $res_articlebase['create_at'];
        $article['lock'] = ArticlesStatus::is_status($article_id,'lock');
        $article['approved'] = ArticlesApproval::getApproved($article_id);
        $article['approved_num'] = ArticlesApprovalCount::getApprovedNum($article_id);
        $article['collected'] = is_null($user_id) ? false : UserCollections::getIsCollected($user_id,$article_id);
        $article['collected_num'] = UserCollections::getCollectedNum($article_id);
        $article['author']['id'] = $res_articlebase['author_id'];
        $article['author']['name'] = $res_articlebase['author_name'];
        $article['author']['articles_num'] = UsersArticlesCount::ArticlesNum($res_articlebase['author_id']);
        $article['author']['avatar_url'] = AvatarUrl::getUrl($res_articlebase['author_id']);
        return response() -> json($article,200) -> setEnCodingOptions(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 发表文章 (A6)
     * POST /articles
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postArticles(Request $request)
    {
        //获得用户id
        $user_id = 1;
        if(empty($user_id)){
            return response(['error' => '未登录，不能操作'],401);
        }
        //从User表中查到登录用户的信息
        $res_author = Users_Base::getUserInfo($user_id);
        //获得将保存到articles_content的文章 title content
        $articlescontent = $request -> all('title','content');
        if(empty($articlescontent)){
            return response(['error' => '创建失败'],400);
        }
        //保存articles_base并获得将要用来保存的文章 id
        $res_articlesbase = new ArticlesBase;
        $res_articlesbase -> author_id = $user_id;
        $res_articlesbase -> author_name = $res_author['name'];
        $res_articlesbase -> content_digest = mb_substr($articlescontent['content'],0,100,'utf-8');
        $res_articlesbase_save = $res_articlesbase -> save();
        if($res_articlesbase_save){
            $res_articlescontent = ArticlesContent::create(
                [
                    'id' => $res_articlesbase -> id,
                    'title' => $articlescontent['title'],
                    'content' => $articlescontent['content']
                ]
            );
            if($res_articlescontent){
                return response(['id' => $res_articlesbase -> id],200);
            }else{
                return response(['error' => '创建失败'],400);
            };
        }else{
            return response(['error' => '创建失败'],400);
        }
    }

    /**
     * 编辑文章 - 文章的编辑操作 (A8)
     * PUT /articles/:id
     * @param null $article_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function putArticles($article_id = NULL)
    {
        $user_id = 1;
        if(empty($user_id)){
            return response(['error' => '未登录，不能操作'],401);
        }
        if(is_null($article_id)){
            return response(['error' => '文章不存在'],404);
        }
        if(ArticlesStatus::is_status($article_id,'delete')){
            return response(['error' => '文章已被删除'],410);
        }
        $author_id = ArticlesBase::getAuthor($article_id);
        if($author_id != $user_id){
            // 缺管理权限判断           if(is_admin($user_id)){}
            return response(['error' => '没有权限操作'],403);
        };
        //接收put过来的数据，并转换成数组
        $res_put = file_get_contents('php://input');
        $res_put = json_decode($res_put,true);
        //查找文章是否存在，如果存在，而开始编辑
        $res_articlescontent = ArticlesContent::find($article_id);
        if($res_articlescontent){
            if($res_articlescontent -> update($res_put)){
                return response(['id' => $article_id],200);
            }else{
                return response(['error' => '编辑失败'],400);
            };
        }else{
            return response(['error' => '文章不存在'],404);
        }
    }



}
