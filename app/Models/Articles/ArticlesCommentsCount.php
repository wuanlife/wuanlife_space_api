<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesCommentsCount extends Model
{
    protected $table = 'articles_comments_count';
    protected $primaryKey = 'article_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'article_id' => 'int',
        'count' => 'int'
    ];

    protected $fillable = [
        'count'
    ];

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
