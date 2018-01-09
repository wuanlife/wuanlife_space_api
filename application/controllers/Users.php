<?php

class Users extends REST_Controller
{
    public function __construct(string $config = 'rest')
    {
        parent::__construct($config);
        $this->load->helper(['form', 'url', 'url_helper']);
        $this->load->library(['form_validation', 'jwt']);
        $this->load->model('users_model');
        $this->form_validation->set_message('required', '{field}必填.');
        $this->form_validation->set_message('min_length', '{field}长度不小于{param}.');
        $this->form_validation->set_message('max_length', '{field}长度不大于{param}.');
        $this->form_validation->set_message('valid_email', '{field}不是合法邮箱地址.');
        $this->form_validation->set_message('is_natural_no_zero', '{field}不是正整数.');
        $this->form_validation->set_message('is_natural', '{field}不是自然数.');
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
            $this->response(['error' => '用户名密码错误，请重试！'], 401);
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
            'id'           => $id,
            'name'         => $name,
            'mail'         => $mail,
            'Access-Token' => $jwt
        ]);
    }

    /**
     * A13 获取用户收藏列表
     * 
     */

    public function collections_get($user_id)
    {
        $data['user_id'] = $user_id;

        $re = $this->users_model->get_collect_articles($data);

        for ($i=0; $i < count($re['article']); $i++) {
            $data['article_id'] = $re['article'][$i]['article_id'];
            $re['article'][$i]['image_url'] = $this->users_model->get_article_img($data);
        }
        // foreach ($re['article'] as $key => $value) {

        //     $ree['article'][$key]=[
        //         'create_time'=>$value['create_at'],
        //         'id'=>$value['article_id'],
        //         'title'=>$value['content'],
        //         'delete'=>$value['status'],


        //     ];
        //     # code...
        // }

        $this->response($re); 
//         //权限校验
//         // $jwt = $this->input->get_request_header('Access-Token', TRUE);
//         // $token = $this->parsing_token($jwt);
//         // if($token->user_id!=$user_id)
//         // {
//         //     $this->response(['error'=>'您没有权限'],403);
//         // }
//         // $user_id = $this->get('id');
//         // 
//            $data = array(
//             'user_id'   => $token->user_id,
//             'limit'     => $this->get('limit')?:20,     //每页显示数
//             'offset'    => $this->get('offset')?:0,     //每页起始数
//         );
//         $this->form_validation->set_data($data);
//         if ($this->form_validation->run('lists') == FALSE)
//             $this->response(validation_errors(),422);

//         //获得用户收藏帖子
//         $re['data']=$this->Post_model->get_collect_post($data);
//         if(empty($re['data'])){
//             $this->response('',204);
//         }
//         $re = $this->Post_model->get_image_url($re);        //解析帖子内容，获得帖子中包含的图片
//         $re = $this->Post_model->delete_image_gif($re);     //删除帖子中gif格式的图片
//         $re = $this->Post_model->post_image_limit($re);     //展示图片限制，目前是显示三张
//         $re = $this->Post_model->delete_html_posts($re);    //从帖子内容去除 HTML 和 PHP 标记
//         $re = $this->Post_model->post_text_limit($re);      //帖子内容长度显示，目前是300字符
// //        $this->response($re);
//         date_default_timezone_set('UTC');
//         foreach($re['data'] as $key=>$value){
//             $re['data'][$key] = [
//                 'id' => $value['articles_content.id'],
//                 'title' => $value['articles_content.title'],
//                 'content' => $value['articles_content.content'],
//                 'author' => [
//                     'name'       =>$value['nickname'],
//                     'id'         =>$value['user_base_id'],
// //                ],
//                 'group' => [
//                     'id'  => $value['group_base_id'],
//                     'name'    => $value['name'],
//                 ],
//             ];
//         }

//         //分页
//         $all_num = $this->Post_model->get_collect_post($data,TRUE);
//         $offset = $data['offset'];
//         $limit = $data['limit'];
//         $page_count  = (ceil($all_num/$limit)-1);                   //比总页数小 1
//         $finallyo = $page_count * $limit;
//         $lasto = ($offset-$limit)>0?($offset-$limit):0;
//         $nexto = ($offset+$limit)<$finallyo?($offset+$limit):$finallyo;
//         $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
//             $_SERVER['HTTP_X_FORWARDED_HOST'] :
//             (isset($_SERVER['HTTP_HOST']) ?
//                 $_SERVER['HTTP_HOST'] :
//                 ''
//             );

//         $re['paging'] = [
//             'first'=> "{$host}/users/{$user_id}/collections?limit={$limit}&offset=0",
//             'previous'=>"{$host}/users/{$user_id}/collections?limit={$limit}&offset={$lasto}",
//             'next'=> "{$host}/users/{$user_id}/collections?limit={$limit}&offset={$nexto}",
//             'final'=> "{$host}/users/{$user_id}/collections?limit={$limit}&offset={$finallyo}"
//         ];

//         $this->response($re);
//     }

     }

}