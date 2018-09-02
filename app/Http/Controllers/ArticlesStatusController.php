<?php

namespace App\Http\Controllers;

use App\Models\Articles\ArticlesBase;
use App\Models\Articles\ArticlesStatus;
use Illuminate\Http\Request;

class ArticlesStatusController extends Controller
{
    /**
     * 锁定文章
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function lock($id)
    {
        $article = ArticlesBase::with('articles_status')->find($id);
        if (!$article) {
            return response(['error' => '该文章不存在'], 404);
        }

        if (!$article->articles_status) {
            $status = $article->articles_status()->create([
                'status' => (1 << 1)
            ]);

            if ($status->id) {
                return response([], 204);
            } else {
                return response(['error' => '锁定失败'], 400);
            }
        }

        if (isset($article->articles_status->status)) {
            $status = $article->articles_status()->update([
                'status' => $article->articles_status->status | (1 << 1)
            ]);
            if (false !== $status) {
                return response([], 204);
            } else {
                return response(['error' => '锁定失败'], 400);
            }
        }


        if (($article->articles_status->status & (1 << ArticlesStatusDetail::where('detail', '删除')->value('status')))) {
            return response(['error' => '该文章已被删除'], 410);
        }

        if ($article->articles_status->status & (1 << ArticlesStatusDetail::where('detail', '锁定')->value('status'))) {
            $this->response(['error' => '该文章已被锁定！'], 400);
        }
    }

    /**
     * 取消锁定
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function unlock($id)
    {
        $article = ArticlesBase::with('articles_status')->find($id);
        if (!$article) {
            return response(['error' => '该文章不存在'], 404);
        }

        if (ArticlesStatus::status($article->articles_status->status, '删除')) {
            return response(['error' => '文章已被删除'], 410);
        }

        $status = $article->articles_status()->update([
            'status' => ($article->articles_status->status & (~(1 << 1)))
        ]);

        if (false !== $status) {
            return response([], 204);
        } else {
            return response(['error' => '取消锁定失败'], 400);
        }
    }
}
