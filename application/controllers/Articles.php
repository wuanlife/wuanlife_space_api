<?php
/**
 * Created by PhpStorm.
 * User: tacer
 * Date: 2017/12/28
 * Time: 19:52
 */

class Articles extends REST_Controller
{
    public function __construct(string $config = 'rest')
    {
        parent::__construct($config);

    }

    public function search_post()
    {
        // 加载搜索模版
        $this->load->model('search_model');
        // 获取url参数列表
        $param = $this->search_model->getSearchParam();
        // 判断参数列表是否完整
        $this->search_model->validate($param) or $this->response(['error' => '缺少必要的参数'], 400);
        // 获取相匹配的数据
        $data = $this->search_model->search($param, 'articles');
        // 返回数据
        $this->response($data, 200);
    }


}