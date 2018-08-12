<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Users;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class UsersCollectionsCount
 * 
 * @property int $user_id
 * @property int $count
 *
 * @package App\Models
 */
class UsersCollectionsCount extends Eloquent
{
	protected $table = 'users_collections_count';
	protected $primaryKey = 'user_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'count' => 'int'
	];

	protected $fillable = [
		'count'
	];
}
