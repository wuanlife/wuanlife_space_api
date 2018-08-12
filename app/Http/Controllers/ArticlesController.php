<?php

namespace App\Http\Controllers;

use App\Models\Articles\ArticlesBase;
use App\Models\Articles\ArticlesStatusDetail;
use App\Models\Articles\Articles_Content;
use App\Models\Articles\ArticlesApproval;
use App\Models\Articles\ArticlesApprovalCount;
use App\Models\Users\UserCollections;

class ArticlesController extends Controller
{
    public function lock($id)
    {
        $article = ArticlesBase::find($id);
        if (!$article) {
            return response(['error' => '该文章不存在'], 404);
        }

        if (!$article->status) {
            $status = $article->status()->create([
                'status' => (1 << 1)
            ]);

            if ($status->id) {
                return response([], 204);
            } else {
                return response(['error' => '锁定失败'], 400);
            }
        }

        if (isset($article->status->status)) {
            $status = $article->status()->update([
                'status' => $article->status->status | (1 << 1)
            ]);
            if (false !== $status) {
                return response([], 204);
            } else {
                return response(['error' => '锁定失败'], 400);
            }
        }


        if (($article->status->status & (1 << ArticlesStatusDetail::where('detail', '删除')->value('status')))) {
            return response(['error' => '该文章已被删除'], 410);
        }

        if ($article->status->status & (1 << ArticlesStatusDetail::where('detail', '锁定')->value('status'))) {
            $this->response(['error' => '该文章已被锁定！'], 400);
        }
    }

    public function unlock($id)
    {
        $article = ArticlesBase::find($id);
        if (!$article) {
            return response(['error' => '该文章不存在'], 404);
        }


        if (($article->status->status & (1 << ArticlesStatusDetail::where('detail', '删除')->value('status')))) {
            return response(['error' => '文章已被删除'], 410);
        }

        $status = $article->status()->update([
            'status' => ($article->status->status & (~(1 << 1)))
        ]);

        if (false !== $status) {
            return response([], 204);
        } else {
            return response(['error' => '取消锁定失败'], 400);
        }
    }


    /**
     * 获取用户文章列表
     * GET /users/:id/articles
     * @param $id
     */
    public function getUsersArticles($id=NULL)
    {
        $res_articlebase = ArticlesBase::find($id);
        echo $id;

        $articles['id'] = $res_articlebase->id;
        $res_articlescontent = Articles_Content::find($id);

        $articles['title'] = $res_articlescontent->title;
        $articles['content'] = $res_articlescontent->content;
        $articles['update_at'] = $res_articlebase->update_at;
        $articles['create_at'] = $res_articlebase->create_at;

        $articles_approve = new ArticlesApproval;
        $articles['approved'] = $articles_approve->getApproved($id);
        $articles_approve_count = new ArticlesApprovalCount;
        $approved_num = $articles_approve_count->getApprovedNum($id);

        $user_collections = new UserCollections;
        $res = $user_collections -> getCollected($id);
        dd($res);


    }




}
