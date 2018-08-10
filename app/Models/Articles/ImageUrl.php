<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ImageUrl
 * 
 * @property int $article_id
 * @property string $url
 * @property int $delete_flg
 *
 * @package App\Models
 */
class ImageUrl extends Eloquent
{
	protected $table = 'image_url';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'article_id' => 'int',
		'delete_flg' => 'int'
	];

	protected $fillable = [
		'article_id',
		'url',
		'delete_flg'
	];
}
