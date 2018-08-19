<?php
/**
 * 评论总数表模型
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/7/31
 * Time: 19:50
 */

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Articles_Comments_Count extends Model
{
    protected $table = "articles_comments_count";//表名称
    protected $primaryKey = "id";//主键
    public $timestamps = false;//不主动更新
    public $incrementing = true;//主键不自增

    /**
     * 新增评论总数
     * @param $articles_id
     * @return bool
     */
    public function add_articles_comments_count($articles_id)
    {
        $this->articles_id = $articles_id;
        $this->count = 0;
        return $this->save();
    }

    /**
     * 根据id获取评论总数
     * @param $articles_id
     * @return mixed
     */
    public function get_articles_comments_count($articles_id)
    {
        return $this->where("articles_id", "=", $articles_id)->first();
    }

    /**
     * 根据文章id直接返回评论数目（int）
     * @param $article_id
     * @return mixed
     */
    public static function getRepliedNum($article_id)
    {
        return self::where('articles_id',$article_id) -> value('count');
    }
}
