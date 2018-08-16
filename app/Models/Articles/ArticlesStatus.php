<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticlesStatus
 * 
 * @property int $id
 * @property int $status
 * @property \Carbon\Carbon $create_at
 *
 * @package App\Models
 */
class ArticlesStatus extends Eloquent
{
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'status' => 'int'
	];

	protected $dates = [
		'create_at'
	];

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

    public static function changeStatus($article_id,$detail)
    {
        //查询某状态对应的status
        $detail_status = ArticlesStatusDetail::getStatus($detail);
        //给当前文章状态赋值
        $res_articlestatus = self::find($article_id);
        $res_articlestatus -> status = $detail_status;
        return $res_articlestatus -> save();
    }
}
