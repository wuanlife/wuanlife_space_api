<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/12 0012
 * Time: 下午 1:45
 */
namespace App\Models\Articles;
use Illuminate\Database\Eloquent\Model;
/**
 * Class ArticlesApproval
 * 
 * @property int $article_id
 * @property int $user_id
 *
 * @package App\Models
 */
class ArticlesApproval extends Model
{
    protected $table = 'articles_approval';
    protected $primaryKey = 'article_id';
    public $incrementing = false;
    public $timestamps = false;
    protected $casts = [
      'article_id' => 'int',
      'user_id' => 'int'
    ];

    /**
     * 获取文章是否已经被点赞
     * @param $id
     * @return bool
     */
    public static function getApproved($id)
    {
        $res = self::where('article_id',$id) -> first();
        return $res ? true : false;
    }
}
