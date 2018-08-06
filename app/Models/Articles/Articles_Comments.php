<?php
/**
 * 评论关系表
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/7/31
 */

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Articles_Comments extends Model
{
    protected $table = "articles_comments";//表名称
    protected $primaryKey = "comment_id";//主键
    public $timestamps = false;//不主动更新
    public $incrementing = true;//主键不自增
    protected $guarded = [];

    /**
     * 新增评论关系
     * @param $data
     * @return mixed
     */
    public function add_articles_comments($data)
    {
        return $this::create($data);
    }

    /**
     * 分页查询评论
     * @param $article_id
     * @param $offset
     * @param $limit
     * @return \Illuminate\Support\Collection
     */
    public function page_articles_comments($article_id, $offset, $limit)
    {
        return DB::table("articles_comments")
            ->where("article_id", "=", $article_id)
            ->select("comment_id", "user_id", "floor", "create_at")
            ->offset($offset)->limit($limit)
            ->get();
    }

    /**
     * 根据评论id获取文章的评论关系
     * @param $comment_id
     * @return mixed
     */
    public function get_articles_comments_count($comment_id)
    {
        return $this->where("comment_id", "=", $comment_id)->first();
    }

    /**
     * 根据文章id与楼层数获取评论关系
     * @param $article_id
     * @param $floor
     * @return mixed
     */
    public function get_articles_comments_count_by_floor($article_id, $floor)
    {
        return $this->where("article_id", "=", $article_id)
            ->where("floor", "=", $floor)
            ->first();
    }


    /**
     * 验证该用户是否是文章作者
     * @param $article_id
     * @param $user_id
     * @return bool
     */
    public function validate($article_id, $user_id)
    {
        $buffers = DB::table("articles_base")
            ->where("id", "=", $article_id)
            ->where("author_id", "=", $user_id)
            ->get();
        if (count($buffers->all()) > 0) {
            return true;
        }
        return false;
    }

    /**
     * 根据评论id删除评论关系
     * @param $comment_id
     * @return int
     */
    public function delete_articles_comments($comment_id)
    {
        return $this::destroy($comment_id);
    }
}