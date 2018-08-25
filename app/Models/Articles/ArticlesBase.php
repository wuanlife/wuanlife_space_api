<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ArticlesBase extends Model
{
    use Searchable;
    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->content->title,
            'content' => $this->content->content,
        ];
        //return $this->only('id', 'title', 'content');
    }
    protected $table = 'articles_base';
    protected $primaryKey = 'id';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
    public $timestamps = true;

    /**
     * @param $value
     * @return false|string
     * 2018/8/6 12:42---aunhappy
     * 格式化创建时间戳
     */
    public function getCreateAtAttribute($value)
    {
        return date('c',strtotime($value));
    }

    /**
     * @param $value
     * @return false|string
     * 2018/8/6 12:43---aunhappy
     * 格式化更新时间戳
     */
    public function getUpdateAtAttribute($value)
    {
        return date('c',strtotime($value));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 2018/8/6 12:42---aunhappy
     * 帖子内容
     */
    public function content()
    {
        return $this->hasOne('App\Models\Articles\ArticlesContent','id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 点赞数
     */
    public function approval_count()
    {
        return $this->hasOne('App\Models\Articles\ArticlesApprovalCount','article_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 收藏数
     */
    public function collections_count()
    {
        return $this->hasOne('App\Models\Articles\ArticlesCollectionsCount','article_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 回复数
     */
    public function comments_count()
    {
        return $this->hasOne('App\Models\Articles\ArticlesCommentsCount','articles_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 是否点赞
     */
    public function approved()
    {
        return $this->hasOne('App\Models\Articles\ArticlesApproval','article_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 是否收藏
     */
    public function collected()
    {
        return $this->hasOne('App\Models\Users\UserCollection','article_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 是否回复
     */
    public function replied()
    {
        return $this->hasOne('App\Models\Articles\ArticlesComment','article_id','id');
    }

    /**
     * @return $this
     * 2018/8/6 20:18---aunhappy
     * 帖子预览图片
     */
    public function articles_image()
    {
        return $this->hasMany('App\Models\Articles\ImageUrl','article_id','id')->select(['article_id','url']);
    }
    public function articles_status()
    {
        return $this->hasMany('App\Models\Articles\ArticlesStatus','id','id')->select(['id','status']);
    }
    public function status()
    {
        return $this->hasOne(ArticlesStatus::class, 'id', 'id');
    }

    /**
     * 通过作者id查询作者的所有文章id
     * @param $author_id
     * @return mixed
     */
    public static function getUsersArticles($author_id)
    {
        return self::where('author_id',$author_id) -> get() -> toArray();
    }

    /**
     * 通过作者id查询作者的所有文章id 带起始offset 和 每页条数limit
     * @param $author_id
     * @return mixed
     */
    public static function getUsersArticlesOffsetLimit($author_id,$offset,$limit)
    {
        return self::where('author_id',$author_id) -> offset($offset) -> limit($limit) -> get() -> toArray();
    }

    /**
     * 通过文章id查询该文章的其它信息
     * @param $article_id
     * @return mixed
     */
    public static function getArticleUser($article_id)
    {
        return self::where('id',$article_id) -> get() -> toArray();
    }

    /**
     * 查找文章id返回作者author_id (int)
     * @param $article_id
     * @return mixed
     */
    public static function getAuthor($article_id)
    {
        return self::where('id',$article_id) -> value('author_id');
    }
}
