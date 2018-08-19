<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticlesStatusDetail
 * 
 * @property int $status
 * @property string $detail
 *
 * @package App\Models
 */
class ArticlesStatusDetail extends Eloquent
{
	protected $primaryKey = 'status';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'detail'
	];

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
