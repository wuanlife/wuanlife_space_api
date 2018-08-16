<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 06 Aug 2018 02:29:28 +0000.
 */

namespace App\Models\Users;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AuthDetail
 * 
 * @property int $id
 * @property string $indentity
 *
 * @package App\Models
 */
class AuthDetail extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'indentity'
	];

    protected $table = 'auth_detail';
}