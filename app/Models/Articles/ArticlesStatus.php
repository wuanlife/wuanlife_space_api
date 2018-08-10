<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticlesStatus
 * 
 * @property int $id
 * @property int $status
 * @property \Carbon\Carbon $create_at
 *
 * @package App\Models
 */
class ArticlesStatus extends Eloquent
{
	protected $table = 'articles_status';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'status' => 'int'
	];

	protected $dates = [
		'create_at'
	];

	protected $fillable = [
		'create_at'
	];
}
