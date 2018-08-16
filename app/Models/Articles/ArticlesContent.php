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

class ArticlesContent extends Model
{
    protected $table = 'articles_content';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int'
    ];

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