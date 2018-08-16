<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesStatus extends Model
{
    protected $table = 'articles_status';
    protected $fillable = ['status'];
    public $timestamps = false;
    const CREATED_AT = 'create_at';

    /**
     * 查询文章的status值
     * @param $article_id
     * @return mixed
     */
    public static function getArticlesStatus($article_id)
    {
        return self::where('id',$article_id) -> value('status');
    }

    /**
     * 判断文章是否处于当前状态
     * @param $article_id
     * @param $detail
     * @return bool
     */
    public static function is_status($article_id,$detail)
    {
        //查询文章id对应的status
        $article_status = self::getArticlesStatus($article_id);
        //查询某状态对应的status
        $detail_status = ArticlesStatusDetail::getStatus($detail);
        //对比是否相等，返回true or false
        return ($article_status == $detail_status) ? true : false ;
    }
}
