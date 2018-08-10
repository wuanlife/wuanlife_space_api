<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticlesComment
 * 
 * @property int $comment_id
 * @property int $article_id
 * @property int $user_id
 * @property int $floor
 * @property \Carbon\Carbon $create_at
 *
 * @package App\Models
 */
class ArticlesComment extends Eloquent
{
	protected $primaryKey = 'comment_id';
	public $timestamps = false;

	protected $casts = [
		'article_id' => 'int',
		'user_id' => 'int',
		'floor' => 'int'
	];

	protected $dates = [
		'create_at'
	];

	protected $fillable = [
		'article_id',
		'user_id',
		'floor',
		'create_at'
	];
}
