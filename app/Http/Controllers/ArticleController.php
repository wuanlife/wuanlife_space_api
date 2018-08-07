<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Articles\Article_approval;

class ArticleController extends Controller
{
    // A2 
    public function approval($article_id)
    {
        $user_id = 1;
        $user = Article_approval::where(['article_id' => $article_id, 'user_id' => $user_id])->first();
        if (empty($user)) {
            $a = new Article_Approval;
            $a->article_id = $article_id;
            $a->user_id = $user_id;
            $a->save();
            return response("点赞成功", 204);
        }
    }
}
