<?php

namespace App\Http\Controllers;

use App\Models\Articles\ArticlesBase;
use App\Models\Articles\ArticlesStatusDetail;

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
}
