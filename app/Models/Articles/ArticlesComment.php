<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesComment extends Model
{
	protected $primaryKey = 'comment_id';
	public $timestamps = false;

    protected $fillable = [
        'article_id',
        'user_id',
        'floor',
        'create_at'
    ];

	protected $dates = [
		'create_at'
	];

    /**
     * 评论详情
     */
    public function content()
    {
        return $this->hasOne(ArticlesCommentContent::class, 'id', 'comment_id');
    }
}
