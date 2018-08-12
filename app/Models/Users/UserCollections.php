<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/12 0012
 * Time: 下午 2:35
 */

namespace App\Models\Users;


use Illuminate\Database\Eloquent\Model;

class UserCollections extends Model
{
    protected $table = 'user_collections';
    protected $primaryKey = 'user_id';
    public $timestamps = 'false';

    public function getCollected($id)
    {
        $res = $this -> where('$user_id',$id) -> get();
        return $res;
    }
}
