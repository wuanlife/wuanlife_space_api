<?php

namespace App\Models\Articles;


use Illuminate\Database\Eloquent\Model;

class ArticlesStatusDetail extends Model
{
    protected $table = 'articles_status_detail';
    protected $primaryKey = 'status';
    public $timestamps = false;

    /**
     * 查询文章状态码详情
     * @param $status
     * @return mixed
     */
    public static function getStatusDetail($status)
    {
        return self::where('status',$status) -> value('detail');
    }

    /**
     * 查询某状态对应的status
     * @param $detail
     * @return mixed
     */
    public static function getStatus($detail)
    {
        return self::where('detail',$detail) -> value('status');
    }
}
