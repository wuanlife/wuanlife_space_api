<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;

class ArticlesBase extends Model
{
    protected $table = 'articles_base';
    public function status()
    {
        return $this->hasOne(ArticlesStatus::class, 'id', 'id');
    }

}
