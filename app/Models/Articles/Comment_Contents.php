<?php
/**
 * 评论表模型
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/7/31
 * Time: 19:43
 */

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment_Contents extends Model
{
    protected $table = "comment_contents";//表名称
    protected $primaryKey = "id";//主键
    public $timestamps = false;//不主动更新
    public $incrementing = true;//主键不自增

    /**
     * 新增评论
     * @param $content
     * @param $articles_id
     * @return mixed
     */
    public function add_comment($content, $articles_id, $user_id)
    {
        //开启事务
        return DB::transaction(function () use ($content, $articles_id, $user_id) {
            $count = new Articles_Comments_Count();
            $articles_comments = new Articles_Comments;
            $data = [
                "article_id" => $articles_id,
                "user_id" => $user_id,
                "floor" => 1
            ];

            if ($count->get_articles_comments_count($articles_id) == null) {
                //新增评论总数
                if (!$count->add_articles_comments_count($articles_id)) {
                    return -1;
                }
            } else {
                //获取floor
                $count_number = $count->get_articles_comments_count($articles_id);
                $data["floor"] = $count_number->count + 1;
            }

            //新增评论关系，再获取到id
            if ($articles_comments->add_articles_comments($data) == null) {
                return -1;
            }

            //新增评论
            $id = ($articles_comments->get_articles_comments_count_by_floor($articles_id, $data["floor"]))["comment_id"];
            $this->content = $content;
            $this->id = $id;
            if ($this->save()) {
                return $id;
            }
            return -1;
        });
    }

    /**
     * 根据id获取评论
     * @param $id
     * @return mixed
     */
    public function get_comment_by_id($id)
    {
        return $this->where("id", "=", $id)->first();
    }

    public function get_comment_id_by_floor()
    {
    }

    /**
     * 根据评论id删除评论
     * @param $comment_id
     * @return mixed
     */
    public function delete_comment($comment_id)
    {
        //开启事务
        return DB::transaction(function () use ($comment_id) {
            $articles_comments = new Articles_Comments();
            if ($articles_comments->delete_articles_comments($comment_id) > 0 &&
                $this::destroy($comment_id) > 0) {
                return true;
            }
            return false;
        });
    }
}



