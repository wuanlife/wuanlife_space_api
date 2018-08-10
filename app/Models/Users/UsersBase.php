<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Users;

use Laravel\Scout\Searchable;
use Reliese\Database\Eloquent\Model as Eloquent;


class UsersBase extends Eloquent
{
    use Searchable;

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            //'email' => $this->email,
        ];
        //return $this->only('id', 'title', 'content');
    }
	protected $table = 'users_base';
	public $timestamps = false;

	protected $dates = [
		'create_at'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'email',
		'name',
		'password',
		'create_at'
	];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 用户头像
     */
    public function avatar_url()
    {
        return $this->hasOne('App\Models\Users\AvatarUrl','user_id','id');
    }
}
