<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesBase extends Model
{
    protected $table = 'articles_base';
    protected $primaryKey = 'id';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    public function status()
    {
        return $this->hasOne(ArticlesStatus::class, 'id', 'id');
    }

    /**
     * 通过作者id查询作者的所有文章id
     * @param $author_id
     * @return mixed
     */
    public static function getUsersArticles($author_id)
    {
        return self::where('author_id',$author_id) -> get() -> toArray();
    }

    /**
     * 通过作者id查询作者的所有文章id 带起始offset 和 每页条数limit
     * @param $author_id
     * @return mixed
     */
    public static function getUsersArticlesOffsetLimit($author_id,$offset,$limit)
    {
        return self::where('author_id',$author_id) -> offset($offset) -> limit($limit) -> get() -> toArray();
    }

    /**
     * 通过文章id查询该文章的其它信息
     * @param $article_id
     * @return mixed
     */
    public static function getArticleUser($article_id)
    {
        return self::where('id',$article_id) -> get() -> toArray();
    }

    /**
     * 查找文章id返回作者author_id (int)
     * @param $article_id
     * @return mixed
     */
    public static function getAuthor($article_id)
    {
        return self::where('id',$article_id) -> value('author_id');
    }
}
