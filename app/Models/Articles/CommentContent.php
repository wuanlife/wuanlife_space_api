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
