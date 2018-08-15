<?php
/**
 * 用户表模型
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/7/31
 * Time: 19:37
 */

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Users_Base extends Model
{
    protected $table = "users_base";//表名称
    protected $primaryKey = "id";//主键

    /**
     * 根据id获取用户信息
     * @param $id
     * @return mixed
     */
    public function get_user($id)
    {
        return $this->where("id", "=", $id)->first();
    }

    /**
     * 根据用户id获取信息，返回数组，如果id不存在并返回Null
     * @param $id
     * @return mixed
     */
    public static function getUserInfo($id)
    {
        $res = self::find($id);
        return  $res ? $res -> toArray() : $res;
    }
}
