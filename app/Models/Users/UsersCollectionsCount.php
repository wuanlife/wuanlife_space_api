<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15
 * Time: 1:40
 */

namespace App\Models\Users;


use Illuminate\Database\Eloquent\Model;

class UsersCollectionsCount extends Model
{
    protected $table = 'users_collections_count';
    protected $primaryKey = 'user_id';

}
