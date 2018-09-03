<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesCommentContent extends Model
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'id',
		'content'
	];
}
