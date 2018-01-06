<?php

class Users extends REST_Controller
{
    public function __construct(string $config = 'rest')
    {
        parent::__construct($config);
    }

    /**
     * 注册
     */
    public function signin_post(): void
    {

    }

    /**
     * 登录
     */
    public function signup_post(): void
    {

    }

    /**
     * 获取用户信息
     * @param $id
     */
    public function users_get($id): void
    {

    }

    /**
     * 修改用户信息
     * @param $id
     */
    public function users_put($id): void
    {

    }

    /**
     * 修改用户密码
     * @param $id
     */
    public function password_put($id): void
    {

    }

    /**
     * 搜索用户
     */
    public function search_post(): void
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