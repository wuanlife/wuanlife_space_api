<?php
/**
 * 文章评论控制器
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/7/31
 * Time: 17:22
 */

namespace App\Http\Controllers;

use App\Models\Articles\Articles_Comments;
use App\Models\Articles\Articles_Comments_Count;
use App\Models\Articles\Users_Base;
use App\Models\Articles\Comment_Contents;
use App\Models\Articles\ArticlesBase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class Articles_Commen extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     * 2018/8/6 15:22---aunhappy
     * 搜索帖子A14
     */
    public function get_articles_search(Request $request)
    {
        $data = [
            'limit'     => $request->input('limit') ?? 20,     //每页显示数
            'offset'    => $request->input('offset') ?? 0,     //每页起始数
            'keyword'     => $request->input('keyword'),       //关键字
            'order'     => $request->input('order') ?? 'asc',
        ];
        $articles_id = ArticlesBase::search($data['keyword'])->keys()->toArray();
        //sort($articles_id);
        $article = ArticlesBase::wherein('articles_base.id',$articles_id)
            ->wherenotin('articles_base.id',function ($query){
                $query->select('articles_status.id')
                ->from('articles_status')
                ->whereRaw('`status` >> 2 & 1 = 1 AND articles_base.id = articles_status.id');
        });

        $articles =  $article->orderBy('update_at',$data['order'])->offset($data['offset'])->limit($data['limit'])->get();

        foreach ($articles as $k=>$v) {
            $rs['articles'][$k]=[
                "id"=>$v->id,
                "title"=>$v->content['title'],
               // "content"=>$v->content['content'],
                "content_digest"=>$v->content_digest,
                "update_at"=>$v->update_at,
                "create_at"=>$v->create_at,
                "author"=>[
                    "avatar_url"=>$v->avatar_url['url'],
                    "name"=>$v->author_name,
                    "id"=>$v->author_id
                ]
            ];
        }
        $rs['total'] = $article->count();
        return $rs;
    }
    /**
     * @param Request $request
     * @return mixed
     * 2018/8/6 12:41---aunhappy
     * A1帖子主页
     */
    public function get_articles_index(Request $request)
    {
        $data = [
            'limit'     => $request->input('limit') ?? 20,     //每页显示数
            'offset'    => $request->input('offset') ?? 0,     //每页起始数
            'order'     => $request->input('order') ?? 'asc',
            'id'        => $request->get('id-token')->uid ?? null,
        ];
        $with = [
            //'content','avatar_url','approval_count','collections_count','comments_count',
            'approved'=>function($query) use ($data) {
                $query->where('user_id',$data['id']);
            },
            'collected'=>function($query) use ($data) {
                $query->where('user_id',$data['id']);
            },
            'replied'=>function($query) use ($data) {
                $query->where('user_id',$data['id']);
            },
            //'articles_status'
        ];
        $article = ArticlesBase::with($with)->whereNotExists(function ($query){
            $query->select('articles_status.id')
                ->from('articles_status')
                ->whereRaw('`status` >> 2 & 1 = 1 AND articles_base.id = articles_status.id');
        });
        $articles =  $article->orderBy('update_at',$data['order'])->offset($data['offset'])->limit($data['limit'])->get();
        foreach ($articles as $k => $v){
            $j = 0;
            $images = [];
            foreach ($v->articles_image as $url) {
                if ( ! empty($url->url) && $j++ < 3) {
                    $images[] = $url->url;
                }
            }
            $rs['articles'][$k]=[
                "id"=>$v->id,
                "title"=>$v->content['title'],
                //"content"=>$v->content['content'],
                "content_digest"=>$v->content_digest,
                "update_at"=>$v->update_at,
                "create_at"=>$v->create_at,
                "approved"=>$v->approved?true:false,
                "approved_num"=>$v->approval_count['count'],
                "collected"=>$v->collected?true:false,
                "collected_num"=>$v->collections_count['count'],
                "replied"=>$v->replied?true:false,
                "replied_num"=>$v->comments_count['count'],
                "image_urls"=>$images,
                "author"=>[
                    "avatar_url"=>$v->avatar_url['url'],
                    "name"=>$v->author_name,
                    "id"=>$v->author_id
                ]
            ];
        }
        $rs['total'] = $article->count();
        return $rs;
    }
    /**
     * 文章评论列表A5
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function get_comments_list($id, Request $request)
    {
        //获取页面容量
        $limit = env("LIMIT");
        if ($request->input("limit") != null) {
            $limit = $request->input("limit");
        }

        //获取起始点
        $offset = 0;
        if ($request->input("offset") != null) {
            $offset = $request->input("offset");
        }

        $datas = [];
        $user_base = new Users_Base();
        $articles_comments = new Articles_Comments();
        $comment_contents = new Comment_Contents();
        $articles_comments_count = new Articles_Comments_Count();

        //拼接成文档约定的格式
        foreach ($articles_comments->page_articles_comments($id, $offset, $limit) as $buffer) {
            $buffer = $this->splicing($user_base->get_user($buffer->user_id), $comment_contents->get_comment_by_id($buffer->comment_id),
                ["floor" => $buffer->floor, "create_at" => $buffer->create_at]);
            array_push($datas, $buffer);

        }
        $datas = [
            "reply" => $datas,
            "total" => ($articles_comments_count->get_articles_comments_count($id))["count"]
        ];
        return response($datas, Response::HTTP_OK);
    }

    /**
     * 评论文章A7
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function add_comments($id, Request $request)
    {
        //表单验证
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|filled|between:1,5000',
        ]);

        if ($validator->fails()) {
            return response(["error" => "表单验证失败"], Response::HTTP_BAD_REQUEST);
        }

        $comments = new Comment_Contents();
        $comment = $request->input("comment");
        $user_id = $request->get("id-token")->uid;

        //保存到数据库
        $comment_id = $comments->add_comment($comment, $id, $user_id);
        if ($comment_id > 0) {
            $user_base = new Users_Base();
            $articles_comments = new Articles_Comments();
            $data = $this->splicing($user_base->get_user($user_id), ["content" => $comment],
                $articles_comments->get_articles_comments_count($comment_id));
            return response($data, Response::HTTP_OK);
        } else {
            return response(["error" => "新增评论失败"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * 删除评论A9
     * @param $id
     * @param $floor
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    function delete_comments($id, $floor, Request $request)
    {
        $user_id = $request->get("id-token");
        $articles_comments = new Articles_Comments();
        $buffer = $articles_comments->get_articles_comments_count_by_floor($id, $floor);

        //验证评论是否存在
        if ($buffer == null) {
            return response(["error" => "评论不存在"], Response::HTTP_NOT_FOUND);
        }

        //验证用户是否有权限进行操作,文章作者与评论者有权删除
        if (!($articles_comments->validate($id, $user_id) || $buffer["user_id"] == $user_id)) {
            var_dump(111);
            return response(["error" => "没有权限操作"], Response::HTTP_FORBIDDEN);
        }
        //开始删除
        $comment_contents = new Comment_Contents();
        if ($comment_contents->delete_comment($buffer["comment_id"])) {
            return response(["删除成功"], Response::HTTP_NO_CONTENT);
        }
        return response(["error" => "删除失败"], Response::HTTP_BAD_REQUEST);
    }

    /**
     * 拼接信息
     * @param $user
     * @param $comment
     * @param $articles_comments
     * @return array
     */
    private function splicing($user, $comment, $articles_comments)
    {
        $data = [
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
            ],
            "comment" => $comment["content"],
            "floor" => $articles_comments["floor"],
            "create_at" => $articles_comments["create_at"]
        ];
        return $data;
    }
}