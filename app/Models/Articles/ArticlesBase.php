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

    /**
     * 查询作者的所有文章ID
     * @param $author_id
     * @return mixed
     */
    public static function getUsersArticles($author_id)
    {
        return self::where('author_id',$author_id) -> get() -> toArray();
    }
}
