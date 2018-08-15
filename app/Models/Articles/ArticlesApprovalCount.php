<?php

namespace App\Models\Articles;


use Illuminate\Database\Eloquent\Model;

class ArticlesApprovalCount extends Model
{
    protected $table = 'articles_approval_count';
    protected $primaryKey = 'article_id';
    public $timestamps = 'false';

    public static function getApprovedNum($id)
    {
        $res = self::where('article_id',$id) -> get();
        if($res){
            return $res[0]->count;
        }else{
            return 0;
        }
    }


}
