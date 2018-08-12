<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Users;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class UserCollection
 * 
 * @property int $user_id
 * @property int $article_id
 * @property \Carbon\Carbon $create_at
 *
 * @package App\Models
 */
class UserCollection extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'article_id' => 'int'
	];

	protected $dates = [
		'create_at'
	];

	protected $fillable = [
		'create_at'
	];
}
