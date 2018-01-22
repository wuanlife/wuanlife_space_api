<?php

class Users extends REST_Controller
{
    public function __construct(string $config = 'rest')
    {
        parent::__construct($config);
        $this->load->helper(['form', 'url', 'url_helper']);
        $this->load->library(['form_validation', 'jwt']);
        $this->load->model(['users_model','articles_model']);
        $this->form_validation->set_message('required', '{field}必填.');
        $this->form_validation->set_message('min_length', '{field}长度不小于{param}.');
        $this->form_validation->set_message('max_length', '{field}长度不大于{param}.');
        $this->form_validation->set_message('valid_email', '{field}不是合法邮箱地址.');
        $this->form_validation->set_message('is_natural_no_zero', '{field}不是正整数.');
        $this->form_validation->set_message('is_natural', '{field}不是自然数.');
    }

        /**
     * 解析jwt，获得用户id（旧的拷贝过来的）
     * @param $jwt
     * @return mixed
     */
    private function parsing_token($jwt)
    {
        try{
            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
            return $token;
        }
        catch(Exception $e)
        {
            return $this->response(['error'=>'未登录，不能操作'],401);
        }
    }

    /**
     * 登录
     */
    public function signin_post(): void
    {
        $data =
            [
                'mail'     => $this->post('mail'),
                'password' => $this->post('password')
            ];

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('login') === false) {
            $this->response(['error' => validation_errors()], 422);
        }

        if ( ! $this->users_model->login($data)) {
            $this->response(['error' => '用户名密码错误，请重试！'], 400);
        }

        $this->responseBaseInfo();
    }

    /**
     * 注册
     */
    public function signup_post(): void
    {
        $data =
            [
                'name'     => $this->post('name'),
                'mail'     => $this->post('mail'),
                'password' => $this->post('password')
            ];

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('register') === false) {
            $this->response(['error' => validation_errors()], 422);
        }

        if ($message = $this->users_model->register($data)) {
            $this->response(['error' => '注册失败，' . $message], 400);
        } else {
            $this->responseBaseInfo();
        }
    }

    /**
     * 获取用户信息
     *
     * @param $id
     */
    public function users_get($id): void
    {
        $info = $this->verifyStatus();

        if (!$this->users_model->userIdExists($id)){
            $this->response(['error' => '用户不存在'],400);
        }
        if ($info->user_id != $id) {
            $this->response(['error' => '非法请求，提供的id与令牌信息不符'], 422);
        }

        $data = $this->users_model->getUserInfo($info->user_id);
        $this->response($data, 200);
    }

    /**
     * 修改用户信息
     *
     * @param $id
     */
    public function users_put($id): void
    {
        $info = $this->verifyStatus();
        if ($info->user_id != $id) {
            $this->response(['error' => '非法请求，提供的id与令牌信息不符'], 422);
        }

        $data =
            [
                'name'       => $this->put('name'),
                'url' => $this->put('avatar_url'),
                'birthday'   => $this->put('birthday'),
                'sex'        => $this->put('sex')
            ];

        if ($this->users_model->modifyInfo($id, $data)) {
            $this->response([], 204);
        } else {
            $this->response(['error' => '修改用户信息失败'], 400);
        }

    }

    /**
     * 修改用户密码
     *
     * @param $id
     */
    public function password_put($id): void
    {
        $info = $this->verifyStatus();
        if ($info->user_id != $id) {
            $this->response(['error' => '非法请求，提供的id与令牌信息不符'], 422);
        }

        $data =
            [
                'old_psd' => $this->put('old_psd'),
                'new_psd' => $this->put('new_psd')
            ];

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('change_psd') === false) {
            $this->response(['error' => validation_errors()], 422);
        }

        if ($message = $this->users_model->modifyPassword($id, $data)) {
            $this->response(['error' => $message], 400);
        } else {
            $this->response([], 204);
        }
    }

    /**
     * 验证用户登陆状态
     * @return stdClass
     */
    private function verifyStatus(): stdClass
    {
        try {
            return
                $this->jwt->decode(
                    $this->input->get_request_header('Access-Token', true),
                    $this->config->item('encryption_key')
                );
        } catch (Exception $e) {
            $this->response(['error' => '身份信息已失效，请重新获取'], 401);
        }
    }

    /**
     * 创建 JwtToken
     *
     * @param int      $user_id
     * @param int|null $exp
     *
     * @return mixed
     */
    private function createJwtToken(int $user_id, int $exp = null)
    {
        $exp   = $exp ?? time() + 3600 * 24 * 7;
        $token = $this->jwt->encode(
            ['exp' => $exp, 'user_id' => $user_id],
            $this->config->item('encryption_key')
        );

        return $token;
    }

    /**
     * 返回用户基本信息
     */
    public function responseBaseInfo(): void
    {
        $id   = $this->users_model->id;
        $name = $this->users_model->name;
        $mail = $this->users_model->mail;
        $jwt  = $this->createJwtToken($id);
        $this->response([
            'id'           => (int)$id,
            'name'         => $name,
            'mail'         => $mail,
            'Access-Token' => $jwt
        ]);
    }




    /**
     * A3 获取用户文章列表
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public function articles_get($user_id)
    {
        // $jwt = $this->input->get_request_header('Access-Token', TRUE);
        // if(empty($jwt)){
        //     $this->response(['error'=>'jwt为空']);
        // }else{
        //     $token = $this->parsing_token($jwt);
        // }
        $data = [
            'user_id' => $user_id,
            'limit'     => $this->get('limit') ?? 20,     //每页显示数
            'offset'    => $this->get('offset') ?? 0,     //每页起始数
        ];

        // $data['user_id'] = $user_id;
        // $data['offset'] = 0;
        // $data['limit'] = 0;

        $data = $this->users_model->get_user_articles($data);
        if(!$data)
        {
            $this->response(['error'=>'获取用户文章列表失败'], 400);
        }

            $this->response($data);
    }

    /**
     * A13 获取用户收藏列表
     * 
     */

    public function collections_get($user_id)
    {
        // $jwt = $this->input->get_request_header('Access-Token', TRUE);
        // if(empty($jwt)){
        //     $this->response(['error'=>'jwt为空']);
        // }else{
        //     $token = $this->parsing_token($jwt);
        // }
        $data = [
            'user_id' => $user_id,
            'limit'     => $this->get('limit') ?? 20,     //每页显示数
            'offset'    => $this->get('offset') ?? 0,     //每页起始数
        ];

        $re = $this->users_model->get_collect_articles($data);

        for ($i=0; $i < count($re['articles']); $i++) {
            $data['article_id'] = $re['articles'][$i]['id'];
            $re['articles'][$i]['image_url'] = $this->users_model->get_article_img($data);
        }

        $this->response($re); 

     }

}