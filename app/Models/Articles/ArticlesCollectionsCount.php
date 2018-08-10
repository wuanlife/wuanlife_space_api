<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesCollectionsCount extends Model
{
    protected $table = 'articles_collections_count';
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
