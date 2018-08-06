<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesStatus extends Model
{
    protected $table = 'articles_status';
    protected $fillable = ['status'];
    public $timestamps = false;
    const CREATED_AT = 'create_at';
}
