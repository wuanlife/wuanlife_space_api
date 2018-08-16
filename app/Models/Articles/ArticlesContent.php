<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesContent extends Model
{
    protected $table = 'articles_content';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'title',
        'content'
    ];

    /**
     * 获得文章Title
     * @param $id
     * @return mixed
     */
    public static function getArticleTitle($id)
    {
        return self::find($id) -> toArray()['title'];
    }

    /**
     * 获得文章具体内容
     * @param $id
     * @return mixed
     */
    public static function getArticleContent($id)
    {
        return self::find($id) -> toArray()['content'];
    }
}
