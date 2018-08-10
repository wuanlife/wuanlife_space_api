<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticlesApproval
 * 
 * @property int $article_id
 * @property int $user_id
 *
 * @package App\Models
 */
class ArticlesApproval extends Eloquent
{
	protected $table = 'articles_approval';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'article_id' => 'int',
		'user_id' => 'int'
	];
}
