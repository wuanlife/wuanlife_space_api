<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesApprovalCount extends Model
{
    protected $table = 'articles_approval_count';
    protected $primaryKey = 'article_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'article_id' => 'int',
        'count' => 'int'
    ];

    protected $fillable = [
        'count'
    ];
}
