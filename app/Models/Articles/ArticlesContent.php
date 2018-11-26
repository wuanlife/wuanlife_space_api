<?php
/**
 * 评论关系表
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/7/31
 */

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ArticlesContent extends Model
{
    protected $table = 'articles_content';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int'
    ];

    protected $fillable = [
        'id',
        'title',
        'content'
    ];

    public function base()
    {
        return $this->belongsTo(ArticlesBase::class, 'id', 'id');
    }

}