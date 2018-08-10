<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ArticlesBase extends Model
{
    use Searchable;

    public function where()
    {

    }
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
     * 帖子作者头像
     */
    public function avatar_url()
    {
        return $this->hasOne('App\Models\Users\AvatarUrl','user_id','author_id');
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
        return $this->hasOne('App\Models\Articles\ArticlesCommentsCount','article_id','id');
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
}
