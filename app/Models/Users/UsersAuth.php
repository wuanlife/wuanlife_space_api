<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Users;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class UsersAuth
 * 
 * @property int $id
 * @property string $name
 * @property int $auth
 *
 * @package App\Models
 */
class UsersAuth extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'auth' => 'int'
	];

	protected $fillable = [
		'name',
		'auth'
	];

    protected $table = 'users_auth';

    function auth()
    {
        return $this->hasOne(AuthDetail::class, 'id', 'auth');
    }
}