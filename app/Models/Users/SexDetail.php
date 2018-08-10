<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Users;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SexDetail
 * 
 * @property int $id
 * @property string $sex
 *
 * @package App\Models
 */
class SexDetail extends Eloquent
{
	protected $table = 'sex_detail';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'sex'
	];
}
