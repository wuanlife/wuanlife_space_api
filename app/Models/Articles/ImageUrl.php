<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15 0015
 * Time: 上午 10:31
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ImageUrl
 * 
 * @property int $article_id
 * @property string $url
 * @property int $delete_flg
 *
 * @package App\Models
 */
class ImageUrl extends Eloquent
{
	protected $table = 'image_url';
  protected $primaryKey = 'article_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'article_id' => 'int',
		'delete_flg' => 'int'
	];

	protected $fillable = [
		'article_id',
		'url',
		'delete_flg'
	];
  
    /**
     * 查询文章中图片的urls
     * @param $article_id
     * @return mixed
     */
    public static function getImageUrls($article_id)
    {
        return self::where('article_id',$article_id) -> get(['url']) -> toArray();
    }
}
