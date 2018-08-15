<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/12 0012
 * Time: 下午 1:45
 */

namespace App\Models\Articles;


use Illuminate\Database\Eloquent\Model;

class ArticlesApproval extends Model
{
    protected $table = 'articles_approval';
    protected $primaryKey = 'article_id';
    public $timestamps = 'false';

    /**
     * 获取文章是否已经被点赞
     * @param $id
     * @return bool
     */
    public static function getApproved($id)
    {
        $res = self::where('article_id',$id) -> first();
        return $res ? true : false;
        /*
        if($res){
            return true;
        }else{
            return false;
        }
        */
    }
}
