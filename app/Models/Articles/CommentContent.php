<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CommentContent
 * 
 * @property int $id
 * @property string $content
 *
 * @package App\Models
 */
class CommentContent extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'content'
	];

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
            $articles_comments = new ArticlesComments();
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
            $articles_comments = new ArticlesComments();
            if ($articles_comments->delete_articles_comments($comment_id) > 0 &&
                $this::destroy($comment_id) > 0) {
                return true;
            }
            return false;
        });
    }
}
