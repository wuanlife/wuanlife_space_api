<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Articles;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticlesStatusDetail
 * 
 * @property int $status
 * @property string $detail
 *
 * @package App\Models
 */
class ArticlesStatusDetail extends Eloquent
{
	protected $table = 'articles_status_detail';
	protected $primaryKey = 'status';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'detail'
	];
}
