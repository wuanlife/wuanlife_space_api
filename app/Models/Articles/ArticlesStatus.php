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
     * 检测文章状态
     * @param $articles_status int 文章状态
     * @param $detail string 状态标识 lock delete
     * @return bool
     */
    public static function status($articles_status, $detail)
    {
        if ($articles_status & (1 << ArticlesStatusDetail::where('detail', $detail)->value('status'))) {
            return true;
        } else {
            return false;
        }
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
