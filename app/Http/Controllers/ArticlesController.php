<?php

namespace App\Http\Controllers;

use App\Models\Articles\ArticlesApproval;
use App\Models\Articles\ArticlesApprovalCount;
use App\Models\Articles\ArticlesBase;
use App\Models\Articles\ArticlesContent;
use App\Models\Articles\ArticlesStatus;
use App\Models\Articles\ArticlesStatusDetail;
use App\Models\Articles\UsersArticlesCount;
use App\Models\Users\AvatarUrl;
use App\Models\Users\UserCollections;
use Illuminate\Http\Request;


class ArticlesController extends Controller
{
    /**
     * A1帖子主页
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request)
    {
        $data = [
            'limit' => $request->input('limit') ?? 10,     //每页显示数
            'offset' => $request->input('offset') ?? 0,     //每页起始数
            'order' => $request->input('order') ?? 'asc',
            'id' => $request->get('id-token')->uid ?? null,
        ];
        $with = [
            'approved' => function ($query) use ($data) {
                $query->where('user_id', $data['id']);
            },
            'collected' => function ($query) use ($data) {
                $query->where('user_id', $data['id']);
            },
            'replied' => function ($query) use ($data) {
                $query->where('user_id', $data['id']);
            },
            'articles_image'
        ];
        $article = ArticlesBase::with($with)->whereNotExists(function ($query) {
            $query->select('articles_status.id')
                ->from('articles_status')
                ->whereRaw('`status` >> 2 & 1 = 1 AND articles_base.id = articles_status.id');
        });
        $articles = $article->orderBy('update_at', $data['order'])->paginate($data['limit'], ['*'], '', $data['offset']);
        foreach ($articles as $article) {
            $images = [];
            foreach ($article->articles_image as $k =>  $url) {
                $images[$k]['url'] = $url->url;
            }
            $user_info = Builder::requestInnerApi(
                env('OIDC_SERVER'),
                "/api/app/users/{$article->author_id}"
            );
            $user = json_decode($user_info['contents']);
            $rs['articles'][] = [
                "id" => $article->id,
                "title" => $article->content['title'],
                "content_digest" => $article->content_digest,
                "update_at" => $article->update_at,
                "create_at" => $article->create_at,
                "approved" => $article->approved ? true : false,
                "approved_num" => $article->approval_count['count'],
                "collected" => $article->collected ? true : false,
                "collected_num" => $article->collections_count['count'],
                "replied" => $article->replied ? true : false,
                "replied_num" => $article->comments_count['count'],
                "image_urls" => $images,
                "author" => [
                    "avatar_url" => $user->avatar_url,
                    "name" => $user->name,
                    "id" => $user->id
                ]
            ];
        }
        $rs['total'] = $articles->total();
        return $rs;
    }

    /**
     * 锁定文章
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * 取消锁定
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
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
     * @param Request $request
     * GET /users/:id/articles
     * @param null $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUsersArticles(Request $request, $id=NULL)
    {
        //如果无用户id返回空
        if(empty($id)){
            return response('',200);
        }
        //用户、作者相关
        $user_info = Builder::requestInnerApi(
            env('OIDC_SERVER'),
            "/api/app/users/{$id}"
        );
        $user = json_decode($user_info['contents']);
        if(is_null($user)){
            return response(['error'=>'获取用户文章列表失败'],400);
        }
        $author['avatar_url'] = $user->avatar_url;
        $author['name'] = $user->name;
        $author['id'] = $id;
        $author['articles_num'] = UsersArticlesCount::ArticlesNum($id);
        //文章相关
        $input = $request -> all();
        $offset = empty($input['offset']) ? 0 : (int)$input['offset'];
        $limit = empty($input['limit']) ? 20 : (int)$input['limit'];

        $articles = ArticlesBase::with(['content', 'approved', 'approval_count', 'collected', 'collections_count', 'comments_count', 'replied', 'articles_image'])->where(['author_id' => $id])->offset($offset)->limit($limit)->get();
        if($articles->isEmpty()){
            return response(['articles' => array()],200);
        }
        $res = [];
        foreach($articles as $article){
            $res[] = [
                'id' => $article->id,
                'title' => $article->content->title,
                'content' => $article->content->content,
                'update_at' => $article->update_at,
                'create_at' => $article->create_at,
                'approved' => $article->approved ? TRUE : FALSE,
                'approved_num' => $article->approval_count->count,
                'collected' => $article->collected ? TRUE : FALSE,
                'collected_num' => $article->comments_count->count,
                'replied' => $article->replied ? TRUE : FALSE,
                'replied_num' => $article->collections_count->count,
                'image_urls' => $article->articles_image,
            ];
        }
        $response['articles'] = $res;
        $response['author'] = $author;
        return response()->json($response,200)->setEncodingOptions(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 文章详情-文章内容(A4)
     * GET /articles/:id
     * @param null $article_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getArticles(Request $request, $article_id=NULL)
    {
        //通过Access-Token获取用户是否登录
        $user_id = $request->get('Access-Token')->uid;
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
        $user_id = $request->get('id-token')->uid;
        if(empty($user_id)){
            return response(['error' => '未登录，不能操作'],401);
        }
        //获得将保存到articles_content的文章 title content
        $articlescontent = $request -> all('title','content');
        if(empty($articlescontent)){
            return response(['error' => '创建失败'],400);
        }
        //保存articles_base并获得将要用来保存的文章 id
        $res_articlesbase = new ArticlesBase;
        $res_articlesbase -> author_id = $user_id;
        $res_articlesbase -> author_name = $request->get('id-token')->uname;
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
    public function putArticles(Request $request, $article_id = NULL)
    {
        $user_id = $request->get('id-token')->uid;
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

    /**
     * 删除文章 (A11)
     * DELETE /articles/:id
     * @param $article_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteArticles(Request $request, $article_id)
    {
        $user_id = $request->get('id-token')->uid;
        if(empty($user_id)){
            return response(['error' => '未登录，不能操作'],401);
        }
        if(is_null($article_id)){
            return response(['error' => '文章不存在'],404);
        }
        $author_id = ArticlesBase::getAuthor($article_id);
        if($author_id != $user_id){
            // 缺管理权限判断           if(is_admin($user_id)){}
            return response(['error' => '没有权限操作'],403);
        };
        if(ArticlesStatus::is_status($article_id,'delete')){
            return response(['error' => '文章已被删除'],410);
        }
        $res_changestatus = ArticlesStatus::changeStatus($article_id,'delete');
        if($res_changestatus){
            return response('删除成功',204);
        }else{
            return response(['error' => '删除失败'],400);
        };
    }

    // A2 点赞
    // articles_approval 表中记录点赞状态
    public function approval($article_id, Request $request)
    {
        if ($request->get('Access-Token') != NULL) {
            //判断用户是否登陆
            $user_id = $request->get('Access-Token')->uid;
            //$user_id = 1;
            $bool = Article_base::find($article_id);
            if (isset($bool)) {
                //判断文章是否存在
                $status = Articles_status::where('id', $article_id)->first()->status;
                if ($status != 4) {
                    //判断文章是否被删除 4为删除
                    $user = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
                    if (!isset($user)) {
                        //判断用户是否已点赞
                        $a = new Article_Approval;
                        $a->article_id = $article_id;
                        $a->user_id = $user_id;
                        $bool = $a->save();
                        if ($bool == true) {
                            return response(['点赞成功'], 200);
                        } else {
                            return response(['点赞失败'], 400);
                        }
                    } else {
                        return response(['已点赞'], 204);
                    }
                } else {
                    return response(['文章已被删除'], 410);
                }
            } else {
                return response(['文章不存在'], 404);
            }
        } else {
            return response(['未登录，不能操作'], 401);
        }
    }

    //A15 取消点赞
    public function del_approval($article_id, Request $request)
    {
        if ($request->get('Access-Token') != NULL) {
            //判断用户是否登陆
            $user_id = $request->get('Access-Token')->uid;
            $bool = Article_base::find($article_id);
            if (isset($bool)) {
                //判断文章是否存在
                $status = Articles_status::where('id', $article_id)->first()->status;
                if ($status != 4) {
                    //判断文章是否被删除 4为删除
                    $user = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
                    if ($user == true) {
                        //判断用户是否已点赞
                        $bool = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->delete();
                        if ($bool == true) {
                            return response(['取消点赞成功'], 204);
                        } else {
                            return response(['取消点赞失败'], 400);
                        }
                    } else {
                        return response(['已取消点赞'], 204);
                    }
                } else {
                    return response(['文章已被删除'], 410);
                }
            } else {
                return response(['文章不存在'], 404);
            }
        } else {
            return response(['未登录，不能操作'], 401);
        }
    }
}
