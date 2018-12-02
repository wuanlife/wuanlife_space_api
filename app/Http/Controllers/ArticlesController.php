<?php

namespace App\Http\Controllers;

use App\Models\Articles\ArticlesApproval;
use App\Models\Articles\ArticlesApprovalCount;
use App\Models\Articles\ArticlesBase;
use App\Models\Articles\ArticlesContent;
use App\Models\Articles\ArticlesStatus;
use App\Models\Articles\ArticlesStatusDetail;
use App\Models\Articles\UsersArticlesCount;
use App\Models\Users\UserCollections;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;


class ArticlesController extends Controller
{
    /**
     * A1 主页
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request)
    {
        $id_token = $request->header('ID-Token');
        $id = $id_token ? json_decode(base64_decode(explode('.', $id_token)[1]))->uid : null;
        $data = [
            'limit' => $request->input('limit') ?? 10,     //每页显示数
            'offset' => $request->input('offset') ?? 0,     //每页起始数
            'order' => $request->input('order') ?? 'asc',
            'id' => $id
        ];
        $page = ($data['offset'] / $data['limit']) + 1;
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
        $articles = $article->orderBy('update_at', $data['order'])->paginate($data['limit'], ['*'], '', $page);
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
        $author['articles_num'] = UsersArticlesCount::ArticlesNum($id) ?? 0;
        //文章相关
        $input = $request -> all();
        $offset = empty($input['offset']) ? 0 : (int)$input['offset'];
        $limit = empty($input['limit']) ? 20 : (int)$input['limit'];
        $page = ($offset / $limit) + 1;

        $articles = ArticlesBase::with(['content', 'approved', 'approval_count', 'collected', 'collections_count', 'comments_count', 'replied', 'articles_image'])
            ->where(['author_id' => $id])
            ->whereNotExists(function ($query) {
                $query->select('articles_status.id')
                    ->from('articles_status')
                    ->whereRaw('`status` >> 2 & 1 = 1 AND articles_base.id = articles_status.id');
            })
            ->paginate($limit, ['*'], '', $page);
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
     * A4 文章详情-文章内容
     * @param Request $request
     * @param $article_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(Request $request, $article_id)
    {
        $user_id = 0;
        if ($request->get('id-token')) {
            $user_id = $request->get('id-token')->uid;
        }
        $article = ArticlesBase::with('collected')->find($article_id);
        if (!$article) {
            return response(['error' => '文章不存在'], 404);
        }
        //查询文章状态是否为delete状态
        if (ArticlesStatus::status($article->status, 'delete')) {
            return response(['error' => '文章已被删除'], 410);
        }
        //用户、作者相关
        $user_info = Builder::requestInnerApi(
            env('OIDC_SERVER'),
            "/api/app/users/{$article->author_id}"
        );
        $user = json_decode($user_info['contents']);
        $res['id'] = $article_id;
        $res['title'] = $article->content->title;
        $res['content'] = $article->content->content;
        $res['update_at'] = $article->update_at;
        $res['create_at'] = $article->create_at;
        $res['lock'] = ArticlesStatus::status(isset($article->articles_status->status) ? $article->articles_status->status : 0, 'lock');
        $res['approved'] = ArticlesApproval::getApproved($article_id);
        $res['approved_num'] = ArticlesApprovalCount::getApprovedNum($article_id);
        $res['collected'] = $article->collected ? TRUE : FALSE;
        $res['collected_num'] = UserCollections::getCollectedNum($article_id);
        $res['author'] = [
            'id' => $article->author_id,
            'name' => $article->author_name,
            'articles_num' => UsersArticlesCount::ArticlesNum($article->author_id),
            'avatar_url' => $user->avatar_url
        ];
        return response()->json($res, 200)->setEnCodingOptions(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 发表文章 (A6)
     * POST /articles
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        //获得用户id
        $user_id = $request->get('id-token')->uid;

        //获得将保存到articles_content的文章 title content
        $article_content = $request -> all('title','content');
        //表单验证
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|filled|between:1,60',
            'content' => 'required|string|filled|between:1,5000',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $error = '';
            foreach ($errors->all() as $message) {
                $error .= $message;
            }
            return response(["error" => $error], Response::HTTP_BAD_REQUEST);
        }
        //保存articles_base并获得将要用来保存的文章 id
        $res_articlesbase = new ArticlesBase;
        $res_articlesbase -> author_id = $user_id;
        $res_articlesbase -> author_name = $request->get('id-token')->uname;
//        $res_articlesbase -> content_digest = mb_substr($articlescontent['content'],0,100,'utf-8');  //旧方法摘要
        //正则出三条正文中的url地址  CC 2018-11-23
        $image_urls_arr = [];
        $i = -1;
        $article_content['content'] = htmlentities($article_content['content'],ENT_QUOTES);
        $article_content['content'] = preg_replace_callback(
            '/<img [^>]*src="([^"]+)"[^>]*>/',
            function ($matches) use (&$image_urls_arr,&$i){
                if ($i < 3){
                    $i++;
                    $image_urls_arr[] = $matches[1];
                }
                return $matches[0];
            },$article_content['content']);

        $res_articlesbase -> content_digest =
            substr(
                strip_tags(
                    preg_replace('/<img [^>]*src="([^"]+)"[^>]*>/','[图片]',$article_content['content'])
                ),
                0,100).'...';

        $res_articlesbase_save = $res_articlesbase -> save();
        if($res_articlesbase_save){
            $res_articlescontent = ArticlesContent::create(
                [
                    'id' => $res_articlesbase -> id,
                    'title' => $article_content['title'],
                    'content' => $article_content['content']
                ]
            );
            //保存摘要3张图片 2018-11-23
            foreach($image_urls_arr as $v){
                $res_imageurl = ImageUrl::create(
                    [
                        'article_id' => $res_articlesbase -> id,
                        'url' => $v,
                        'delete_flg' =>0
                    ]
                );
            }
            if($res_articlescontent){
                return response(['id' => $res_articlesbase -> id],200);
            }else{
                return response(['error' => '创建失败'],400);
            };

/*冲突报的有点奇怪，没见过这段代码，暂留
        // 判断用户文章数
        if (!UsersArticlesCount::ArticlesNum($user_id)) {
            $user_articles_count = new UsersArticlesCount();
            $user_articles_count->user_id = $user_id;
            $user_articles_count->save();
        }
        $id = ArticlesBase::max('id');
        $article_content = ArticlesContent::create([
            'id' => $id + 1,
            'title' => $article_content['title'],
            'content' => $article_content['content']
        ]);

        $article_content->base()->create([
            'author_id' => $user_id,
            'author_name' => $request->get('id-token')->uname,
            'content_digest' => mb_substr($article_content['content'],0,100,'utf-8')
        ]);

        if($article_content){
            return response(['id' => $article_content -> id],200);
*/
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
        if(ArticlesStatus::status($article_id,'删除')){
            return response(['error' => '文章已被删除'],410);
        }
        $author_id = ArticlesBase::getAuthor($article_id);
        if($author_id != $user_id){
            // 缺管理权限判断           if(is_admin($user_id)){}
            return response(['error' => '没有权限操作'],403);
        };

        //获得将保存到articles_content的文章 title content
        $article_content = $request -> all('title','content');
        //表单验证
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|filled|between:1,60',
            'content' => 'required|string|filled|between:1,5000',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $error = '';
            foreach ($errors->all() as $message) {
                $error .= $message;
            }
            return response(["error" => $error], Response::HTTP_BAD_REQUEST);
        }
        //查找文章是否存在，如果存在，而开始编辑
        $res_articlescontent = ArticlesContent::find($article_id);
        if($res_articlescontent){
            if($res_articlescontent->update($article_content)){
                $article_base = ArticlesBase::find($article_id);
                $article_base->content_digest = mb_substr($article_content['content'],0,100,'utf-8');
                if ($article_base->save()) {
                    return response(['id' => $article_id],200);
                } else {
                    return response(['error' => '编辑失败'],400);
                }
            }else{
                return response(['error' => '编辑失败'],400);
            }
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
        $article = ArticlesBase::with('articles_status')->find($article_id);
        if($article->author_id != $user_id){
            // 缺管理权限判断
            return response(['error' => '没有权限操作'],403);
        };
        if($article->articles_status && ArticlesStatus::status($article->articles_status->status,'删除')){
            return response(['error' => '文章已被删除'],410);
        }

        if ($article->articles_status) {
            $status = $article->articles_status->status | (1 << ArticlesStatusDetail::where('detail', '删除')->value('status'));
        } else {
            $status = (1 << ArticlesStatusDetail::where('detail', '删除')->value('status'));
        }
        if (!$article->articles_status) {
            $article_status = new ArticlesStatus();
            $article_status->id = $article_id;
            $article_status->status = $status;
            $res = $article_status->save();
        } else {
            $res = ArticlesStatus::where('id', $article_id)->update(['status' => $status]);
        }
        if($res){
            return response('删除成功',204);
        }else{
            return response(['error' => '删除失败'],400);
        }
    }

    /**
     * A2 点赞文章
     * @param Request $request
     * @param $article_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function approval(Request $request, $article_id)
    {
        if ($request->get('id-token') == null) {
            return response(['未登录，不能操作'], 401);
        }
        //判断用户是否登陆
        $user_id = $request->get('id-token')->uid;
        $article = ArticlesBase::find($article_id);
        if (!$article) {
            return response(['文章不存在'], 404);
        }
        // 判断文章是否被删除
        $article_status = ArticlesStatusDetail::where('detail', '删除')->value('status');
        if (isset($article->articles_status->status) && $article->articles_status && ($article->articles_status->status & (1 << $article_status))) {
            return response(['error' => '该文章已被删除'], 410);
        }

        $user = ArticlesApproval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
        if ($user) {// 判断用户是否已点赞
            return response(['已点赞'], 204);
        }
        $approval = new ArticlesApproval;
        $approval->article_id = $article_id;
        $approval->user_id = $user_id;
        if ($approval->save()) {
            return response(['点赞成功'], 204);
        } else {
            return response(['点赞失败'], 400);
        }
    }

    //A15 取消点赞
    public function del_approval($article_id, Request $request)
    {
        if ($request->get('id-token') != NULL) {
            //判断用户是否登陆
            $user_id = $request->get('id-token')->uid;
            $bool = ArticlesBase::find($article_id);
            if (isset($bool)) {
                //判断文章是否存在
                $status = ArticlesStatus::where('id', $article_id)->first();
                if ($status) {
                    $status = $status->status;
                }
                if ($status != 4) {
                    //判断文章是否被删除 4为删除
                    $user = ArticlesApproval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
                    if ($user == true) {
                        //判断用户是否已点赞
                        $bool = ArticlesApproval::where(['article_id' => $article_id, 'user_id' => $user_id])->delete();
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


    /**
     * A14 搜索文章
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(Request $request)
    {
        $data = [
            'limit'     => $request->input('limit') ?? 20,     //每页显示数
            'offset'    => $request->input('offset') ?? 0,     //每页起始数
            'keyword'     => $request->input('keyword'),       //关键字
            'order'     => $request->input('order') ?? 'asc',
        ];
        if (!$data['keyword']) {
            return response(["error" => '缺少keyword'], Response::HTTP_BAD_REQUEST);
        }
        $articles_id = ArticlesBase::search($data['keyword'])->keys()->toArray();
        //sort($articles_id);
        $article = ArticlesBase::wherein('articles_base.id',$articles_id)
            ->wherenotin('articles_base.id',function ($query){
                $query->select('articles_status.id')
                    ->from('articles_status')
                    ->whereRaw('`status` >> 2 & 1 = 1 AND articles_base.id = articles_status.id');
            });

        $articles =  $article->orderBy('update_at',$data['order'])->offset($data['offset'])->limit($data['limit'])->get();
        if ($articles->isEmpty()) {
            return response(['articles' => [], 'total' => 0], Response::HTTP_OK);
        }
        foreach ($articles as $k=>$v) {
            $user_info = Builder::requestInnerApi(
                env('OIDC_SERVER'),
                "/api/app/users/{$v->author_id}"
            );
            $user = json_decode($user_info['contents']);
            $rs['articles'][$k]=[
                "id"=>$v->id,
                "title"=>$v->content['title'],
                // "content"=>$v->content['content'],
                "content_digest"=>$v->content_digest,
                "update_at"=>$v->update_at,
                "create_at"=>$v->create_at,
                "author"=>[
                    "avatar_url"=>$user->avatar_url,
                    "name"=>$user->name,
                    "id"=>$user->id
                ]
            ];
        }
        $rs['total'] = $article->count();
        return $rs;
    }
}
