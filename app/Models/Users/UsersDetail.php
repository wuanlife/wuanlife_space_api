<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Users;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class UsersDetail
 * 
 * @property int $id
 * @property int $sex
 * @property \Carbon\Carbon $birthday
 *
 * @package App\Models
 */
class UsersDetail extends Eloquent
{
	protected $table = 'users_detail';
	public $timestamps = false;

	protected $casts = [
		'sex' => 'int'
	];

	protected $dates = [
		'birthday'
	];

	protected $fillable = [
		'sex',
		'birthday'
	];
}
