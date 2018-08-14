<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/14 0014
 * Time: 下午 7:01
 */

namespace App\Models\Users;


use Illuminate\Database\Eloquent\Model;

class AvatarUrl extends Model
{
    protected $table = 'avatar_url';
    protected $primaryKey = 'user_id';
    public $timestamps = 'false';

    public static function getUrl($user_id)
    {
        return self::where('user_id',$user_id)->value('url');
    }

}
