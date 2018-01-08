<?php
/**
 * Created by PhpStorm.
 * User: tacer
 * Date: 2018/1/7
 * Time: 17:11
 */

class Search extends REST_Controller
{
    /**
     * 搜索文章
     */
    public function articles_post(): void
    {
        // 加载搜索模版
        $this->load->model('search_model');
        // 获取url参数列表
        $param = $this->search_model->getSearchParam();
        // 判断参数列表是否完整
        $this->search_model->validateSearchParam($param) or $this->response(['error' => '缺少必要的参数'], 400);
        // 获取相匹配的数据
        $data = $this->search_model->search($param, 'articles');
        // 返回数据
        $this->response($data, 200);
    }

    /**
     * 搜索用户
     */
    public function users_post(): void
    {
        // 加载搜索模版
        $this->load->model('search_model');
        // 获取url参数列表
        $param = $this->search_model->getSearchParam();
        // 判断参数列表是否完整
        $this->search_model->validateSearchParam($param) or $this->response(['error' => '缺少必要的参数'], 400);
        // 获取相匹配的数据
        $data = $this->search_model->search($param, 'users');
        // 返回数据
        $this->response($data, 200);
    }
}