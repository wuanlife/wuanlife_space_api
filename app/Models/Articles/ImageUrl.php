<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15 0015
 * Time: 上午 10:31
 */

namespace App\Models\Articles;


use Illuminate\Database\Eloquent\Model;

class ImageUrl extends Model
{
    protected $table = 'image_url';
    protected $primaryKey = 'article_id';
    public $timestamps = 'false';

    public static function getImageUrls($article_id)
    {
        return self::where('article_id',$article_id) -> get(['url']) -> toArray();
    }
}
