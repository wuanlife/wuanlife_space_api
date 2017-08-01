<?php


class User extends REST_Controller
{
    /**
     * 构造函数，提前运行
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url','url_helper'));
        $this->load->library(array('form_validation','jwt'));
        $this->load->model(array('User_model','Group_model','Common_model'));
        $this->form_validation->set_message('required', '{field}必填.');
        $this->form_validation->set_message('min_length', '{field}长度不小于{param}.');
        $this->form_validation->set_message('max_length', '{field}长度不大于{param}.');
        $this->form_validation->set_message('valid_email', '{field}不是合法邮箱地址.');
        $this->form_validation->set_message('is_natural_no_zero', '{field}不是正整数.');
        $this->form_validation->set_message('is_natural', '{field}不是自然数.');

    }

    /**
     * 测试接口是否畅通
     */
	public function index_get(){
		echo '接口测试<br>登录接口url：<br>';
		echo 'POST /users/signin';
	}

    /**
     * 返回JSON数据到前端  已有相同方法，后续会移除这里的代码*2017/7/25 0025
     * @param $data
     * @param int $ret
     * @param null $msg
     *
     */
    /**
 *    public function response($data=NULL,$ret=200,$msg=NULL){
        $response=array('ret'=>$ret,'data'=>$data,'msg'=>$msg);
        $this->output
            ->set_status_header($ret)
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: 0')
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))
            ->_display();
        exit;
    }*/

    /**
     * 登录成功后更新用户的信息状态，尤其是加密密码
     * @param $password
     * @param $user_id
     */
    private function update_user_status($password,$user_id,$update = FALSE){
        $user_info = $this->User_model->get_user_information($user_id);
        if(empty($user_info['profile_picture'])){
            $profile_picture = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            $this->db->update('user_detail',['profile_picture' => $profile_picture],"user_base_id = {$user_id}");
//            $this->db->set('profile_picture', $profile_picture);
//            $this->db->where('user_base_id', $user_id);
//            $this->db->update('user_detail');
        }
        date_default_timezone_set('Asia/Shanghai');
        if(empty($user_info['new_password'])){
            $password = password_hash($password,PASSWORD_DEFAULT);
            $this->db->replace('user_password',[
                'user_base_id'      =>$user_id,
                'new_password'      =>$password,
                'create_time'       =>date('Y-m-d H:i:s')
            ]);
        }elseif($update){
            $password = password_hash($password,PASSWORD_DEFAULT);
            $this->db->update('user_password',[
                'new_password'      =>$password,
                'modify_time'       =>date('Y-m-d H:i:s')
            ],['user_base_id'=>$user_id,]);
        }

    }
    /**
     * 登录接口
     * @desc 用于验证并登录用户
     */
    public function login_post(){
        //输入参数校验
        $data=array(
            'email' => $this->post('mail'),
            'password' => $this->post('password'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('login') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        //校验身份并登录
        $model= $this->User_model->user_email($data);
        if(!$model){
            $this->response(['error'=>'该邮箱尚未注册！'],400);
        }elseif(md5($data['password'])!=$model['password']){
            $this->response(['error'=>'密码错误，请重试！'],401);
        }else{
            $this->update_user_status($data['password'],$model['id']);
            $this->response([
                    'Access-Token'=>$this->get_user_token($model['id'],time()+604800),
                    'id' => $model['id'],
                    'name' => $model['nickname'],
                    'mail' => $model['email']
            ]);
        }
    }

    /**
     * 查询用户邀请码
     * @param $id
     * @param bool $continue
     * @return mixed
     */
    private function show_code($id,$continue = FALSE){
        $rs = $this->User_model->show_code($id);
        if(empty($rs)){
            $this->load->helper('icode');
            $i_code = create_code(6);
            $this->User_model->create_code($id,$i_code);
            $rs = $this->User_model->show_code($id);
        }
        if($continue === FALSE)
        {
            $this->response($rs);
        }
        return $rs;
    }

    /**
     * 注册接口-用于验证并注册用户
     */
    public function reg_post(){
        //输入参数校验
        $data=array(
            'nickname'=>$this->post('name'),
            'email'=>$this->post('mail'),
            'password'=>$this->post('password'),
            'code' =>$this->post('code'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('reg') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        //注册信息正确性校验
        $user_email=$this->User_model->user_email($data);
        $user_nickname=$this->User_model->user_nickname($data['nickname']);
        $invite_code = $this->User_model->invite_code($data['code']);
        if(!empty($user_email)){
            $this->response(['error'=>'该邮箱已注册！'],422);
        }elseif (!empty($user_nickname)){
            $this->response(['error'=>'该昵称已注册！'],422);
        }elseif(empty($invite_code)||$invite_code['used']==0){
            $this->response(['error'=>'邀请码已过期或不存在！'],422);
        }else{
            unset($data['code']);
            $data['password'] = md5($data['password']);
            $user_id=$this->User_model->reg($data);
            if($user_id){
                $this->response([
                    'Access-Token'=>$this->get_user_token($user_id,time()+604800),
                    'id' => $user_id,
                    'name' => $data['nickname'],
                    'mail' => $data['email']
                ]);
            }else{
                $this->response(['error'=>'注册失败！'],400);
            }

        }
    }

    /**
     * 注销接口     注销交给前端，后续会移除这里的代码*2017/7/25 0025
     * @desc 用于清除用户登录状态
     * @return int code 操作码，1表示注销成功，0表示注销失败
     * @return string msg 提示信息
     *
     *
     */
    /**
 *    public function logout(){
        $re['code']='1';
        $msg='注销成功！';
        $this->response($re,200,$msg);
    }*/

    /**
     * 获取用户信息
     * @param $user_id
     */
    public function user_info_get($user_id){
        //判断是否是单独获取用户邀请码
        $code = $this->get('field')=='code'?$this->show_code($user_id):$this->show_code($user_id,TRUE);

        //身份信息校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //获取用户信息
        $user = $this->User_model->get_user_information($user_id);

        //数据库性别信息转换
        switch ((int)$user['sex']) {
            case 0:
                $sex = 'secret';
                break;
            case 1:
                $sex = 'man';
                break;
            case 2:
                $sex = 'woman';
                break;
            default:
                $sex = 'secret';
        }
        date_default_timezone_set('UTC');
        $this->response([
            'id'=>$user['id'],
            'sex'=>$sex,
//            'birthday'=>date('Y-m-d\TH:i:s\Z',strtotime("{$user['year']}-{$user['month']}-{$user['day']}")),
            'birthday'=>date('Y-m-d\TH:i:s\Z',$user['birthday']),
            'mail_checked'=>$user['mail_checked']?TRUE:FALSE,
            'avatar_url'=>$user['profile_picture']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2/1/w/100/h/100',
            'mail'=>$user['email'],
            'name'=>$user['nickname'],
            'code'=>$code['code'],
        ]);

    }

    /**
     * 修改用户信息接口
     * @desc 修改用户的信息
     * @param $user_id
     */
    public function user_info_put($user_id){
        //用户身份信息校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data = [
            'name'      =>$this->put('name'),
            'avatar_url'    =>$this->put('avatar_url'),
            'sex'       =>$this->put('sex'),
            'birthday'  =>$this->put('birthday')
        ];
        $this->form_validation->set_message('regex_match', '{field}格式不正确.');
        $this->form_validation->set_message('in_list', '{field}必须包含{param}其中一个.');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('user_info') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        //性别信息输入量转换
        switch ($this->put('sex')) {
            case 'secret':
                $sex = 0;
                break;
            case 'man':
                $sex = 1;
                break;
            case 'woman':
                $sex = 2;
                break;
            default:
                $sex = 0;
        }

        //获取用户信息
        $user = $this->User_model->get_user_information($user_id);
        date_default_timezone_set('UTC');

        $data = array(
            'user_base_id'          =>$user_id,
            'nickname'              =>$this->put('name')?$this->put('name'):$user['nickname'],
            'profile_picture'       =>$this->put('avatar_url')?$this->put('avatar_url'):$user['profile_picture'],
            'sex'           =>$this->put('sex')?$sex:$user['sex'],
            'birthday'      =>strtotime($data['birthday']),
//            'year'          =>$this->put('birthday')?date('Y',strtotime($this->put('birthday'))):$user['year'],
//            'month'         =>$this->put('birthday')?date('m',strtotime($this->put('birthday'))):$user['month'],
//            'day'           =>$this->put('birthday')?date('d',strtotime($this->put('birthday'))):$user['day'],
        );
        $msg=$this->User_model->alter_user_info($data);
//        switch ($msg)
//        {
//            case ['m'=>1,'n'=>1]:
//                $rs = '修改成功';
//                break;
//            case ['m'=>1,'n'=>2]:
//                $rs = '其他资料修改成功，用户名修改失败';
//                break;
//            case ['m'=>1,'n'=>3]:
//                $rs = '其他资料修改成功，用户名被占用';
//                break;
//            case ['m'=>2,'n'=>1]:
//                $rs = '其他资料修改失败，用户名修改成功';
//                break;
//            case ['m'=>2,'n'=>2]:
//                $rs = '修改失败';
//                break;
//            case ['m'=>2,'n'=>3]:
//                $rs = '其他资料修改失败，用户名被占用';
//                break;
//            case ['m'=>1]:
//                $rs = '修改成功';
//                break;
//            case ['m'=>2]:
//                $rs = '修改失败';
//                break;
//            default :
//                $rs = '修改异常，请重试！';
//        }
//        if($msg['m']===1){
//            $this->response(['success'=>$rs]);
//        }
//        if($msg['m']===2){
//            $this->response(['error'=>$rs],400);
//        }
        if($msg['m']===1){
            $this->response(['success'=>'修改成功']);
        }
        if($msg['m']===2){
            $this->response(['error'=>'修改失败'],400);
        }

    }

    /**
     * 获取帖子通知
     * @param $data
     * @return array
     */
    private function get_reply_message($data){
        //将消息列表转化为已读
        $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
        $status = 1;
        $table = 'message_reply';
        $this->alter_status($value,$status,$table);

        //获取消息详情
        $limit = $data['limit'];
        $offset = $data['offset'];
        $model= $this->User_model;
        //帖子可能被删除，相应的通知也会被删除，所以这里不是数据库所有数据返回
        $rs['data'] = $model->show_reply_message($data['user_id'],$limit,$offset);
        foreach($rs['data'] as $key=>$value){

            $re['data'][$key]['user']=array(
                'id'=>$value['user_id'],
                'name'=>$value['user_name'],
            );
            $re['data'][$key]['post']=array(
                'id'=>$value['post_id'],
                'title'=>$value['p_title'],
                'reply_floor'=>$value['reply_floor'],
                'page'=>$this->Common_model->get_post_reply_page($value['post_id'],$value['reply_floor']),
            );
            $re['data'][$key]['message']=array(
                'id'=>$value['m_id'],
                'image_url'=>$value['profile_picture']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100', //没有头像给默认头像
            );
        }

        //分页
        //帖子可能被删除，相应的通知也会被删除，所以这里不是数据库所有数据返回
        $all_num = $model->get_num_message($data['user_id'],$table,FALSE);     //消息总数
        $page_count  = (ceil($all_num/$limit)-1);                   //比总页数小 1
        $finallyo = $page_count * $limit;
        $lasto = ($offset-$limit)>0?($offset-$limit):0;
        $nexto = ($offset+$limit)<$finallyo?($offset+$limit):$finallyo;
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                ''
            );
        $re['paging'] = [
            'first'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset=0&type=post",
            'previous'=>"{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$lasto}&type=post",
            'next'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$nexto}&type=post",
            'final'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$finallyo}&type=post"
        ];

        return $re;
    }

    /**
     * 获取星球通知
     * @param $data
     * @return array
     */
    private function get_notice_message($data){
        //将消息列表转化为已读
        $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
        $status = 1;
        $table = 'message_notice';
        $this->alter_status($value,$status,$table);

        //获取消息详情
        $limit = $data['limit'];
        $offset = $data['offset'];
        $model = $this->User_model;
        $re = array();
        $rs['data'] = $model->show_notice_message($data['user_id'],$limit,$offset);
        foreach($rs['data'] as $key => $value){
            $re['data'][$key]['content'] = $this->message_array($value['type'],$value['user_name'],$value['g_name']);

            //判断应该给星球头像还是用户头像
            if(in_array($value['type'],array(4,5))){
                $image = $model->get_user_information($value['user_id'])['profile_picture'];
            }elseif(in_array($value['type'],array(1,2,3))){
                $image = $this->Group_model->get_group_infomation($value['group_id'])['g_image'];
            }else{
                $image = FALSE;
            }

            $re['data'][$key]['user']=array(
                'id'=>$value['user_id'],
                'name'=>$value['user_name'],
            );
            $re['data'][$key]['group']=array(
                'id'=>$value['group_id'],
                'name'=>$value['g_name'],
            );
            $re['data'][$key]['message']=array(
                'id'=>$value['m_id'],
                'type'=>$value['type'],
                'status'=>$value['status'],
                'image_url'=>$image?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',//没有头像给默认头像
            );
        }

        //分页
        $all_num = $model->get_num_message($data['user_id'],$table);     //消息总数
        $page_count  = (ceil($all_num/$limit)-1);                   //比总页数小 1
        $finallyo = $page_count * $limit;
        $lasto = ($offset-$limit)>0?($offset-$limit):0;
        $nexto = ($offset+$limit)<$finallyo?($offset+$limit):$finallyo;
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                ''
            );
        $re['paging'] = [
            'first'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset=0&type=group",
            'previous'=>"{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$lasto}&type=group",
            'next'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$nexto}&type=group",
            'final'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$finallyo}&type=group"
        ];

        return $re;
    }

    /**
     * 获取私密星球申请通知
     * @param $data
     * @return array
     */
    private function get_apply_message($data){
        //将消息列表转化为已读
        $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
        $status = 1;
        $table = 'message_apply';
        $this->alter_status($value,$status,$table);

        //获取消息详情
        $limit = $data['limit'];
        $offset = $data['offset'];
        $model= $this->User_model;
        $re = array();
        $rs['data'] = $model->show_apply_message($data['user_id'],$limit,$offset);
        foreach($rs['data'] as $key=>$value){

            $re['data'][$key]['user']=array(
                'id'=>$value['user_id'],
                'name'=>$value['user_name'],
            );
            $re['data'][$key]['group']=array(
                'id'=>$value['group_id'],
                'name'=>$value['g_name'],
            );
            $re['data'][$key]['message']=array(
                'id'=>$value['m_id'],
                'status'=>$value['status'],
                'image_url'=>$value['profile_picture']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',//没有头像给默认头像
                'text'=>$value['text'],
            );
        }

        //分页
        $all_num = $model->get_num_message($data['user_id'],$table);     //消息总数
        $page_count  = (ceil($all_num/$limit)-1);                   //比总页数小 1
        $finallyo = $page_count * $limit;
        $lasto = ($offset-$limit)>0?($offset-$limit):0;
        $nexto = ($offset+$limit)<$finallyo?($offset+$limit):$finallyo;
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                ''
            );
        $re['paging'] = [
            'first'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset=0&type=apply",
            'previous'=>"{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$lasto}&type=apply",
            'next'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$nexto}&type=apply",
            'final'=> "{$host}/users/{$data['user_id']}/messages?limit={$limit}&offset={$finallyo}&type=apply"
        ];

        return $re;
    }

    /**
     * 获取用户消息列表，主页
     * @param $data
     * @return mixed
     */
    private function get_index_message($data){
        $data['offset'] = 0;
        $data['limit'] = 1;
        error_reporting(0);
        $rs['data'] =array(
            'group'=>$this->get_notice_message($data)['data'][0] ,
            'apply'=>$this->get_apply_message($data)['data'][0] ,
            'post'=>$this->get_reply_message($data)['data'][0] ,
        );

        //分页
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                ''
            );
        $rs['paging'] = [
            'first'=> "{$host}/users/{$data['user_id']}/messages?limit=1&offset=0",
            'previous'=>"{$host}/users/{$data['user_id']}/messages?limit=1&offset=0",
            'next'=> "{$host}/users/{$data['user_id']}/messages?limit=1&offset=0",
            'final'=> "{$host}/users/{$data['user_id']}/messages?limit=1&offset=0"
        ];

        return $rs;
    }

    /**
     * 用户消息列表展示
     * m_type post帖子通知 group星球通知 apply私密星球申请 home消息列表主页
     * @param $user_id
     */
    public function show_message_get($user_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数验证
        $data=array(
            'user_id'   => $user_id,
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
            'type'      => $this->get('type')?:'home'   //消息类型
        );
        $this->form_validation->set_message('in_list', '{field}必须包含{param}其中一个.');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('message') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        //分类型获取消息
        if($data['type']=='post'){
            $rs = $this->get_reply_message($data);
        }elseif($data['type']=='group'){
            $rs = $this->get_notice_message($data);
        }elseif($data['type']=='apply'){
            $rs = $this->get_apply_message($data);
        }elseif($data['type']=='home'){
            $rs = $this->get_index_message($data);
        }
        if(!empty($rs['data'])){
            $this->response($rs,200);
        }else{
            $this->response('',204);
        }
    }

    /**
     * 获取用户星球通知不同类型的拼接消息
     * @param $type 1同意加入 2拒绝加入 3移出星球 4退出星球 5加入星球
     * @param $user_name
     * @param $g_name
     * @return mixed
     */
    private function message_array($type,$user_name,$g_name){
        $array = array(
            1=>"同意了你的加入申请".$g_name,
            2=>"拒绝了你的加入申请".$g_name,
            3=>"你被移出了星球".$g_name,
            4=>$user_name."退出了".$g_name,
            5=>$user_name."加入了".$g_name
        );
        return $array["$type"];
    }

    /**
     * 修改消息的状态
     * @param $value
     * @param $status int 0未读 1已读 2删除/同意 3拒绝
     * @param $table string 数据表名称
     * @return bool
     */
    private function alter_status($value,$status,$table){
        $model = $this->User_model;
        return $model->alter_status($value,$status,$table);
    }

    /**
     * 处理加入私密星球的申请
     * @param $id
     * @param $mid
     */
    public function process_apply_post($id,$mid){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data = array(
            'user_id'       => $id,
            'm_id'   => $mid,
            'mark'            => $this->post('is_apply'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('process_apply') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //处理申请信息
        $info = $this->get_message_info($data['m_id']);
        $founder_id = $this->Group_model->get_group_infomation($info['group_base_id'])['user_base_id'];
        $founder_id != $data['user_id'] and $this->response(['error'=>'您不是创建者，没有权限！'],403);

        empty($info) and $this->response(['error'=>'操作失败！消息不存在！'],404);
        in_array($info['status'],[2,3]) and $this->response(['error'=>'操作失败！您已同意/拒绝该成员的申请！'],409);
        $info['status'] ==0 and $this->response(['error'=>'操作失败！请查看申请内容后再操作！'],400);
        if($this->Common_model->judge_group_user($info['group_base_id'],$info['user_apply_id']))
        {
            $this->alter_status(['id'=>$data['m_id']],2,'message_apply');
            $this->response(['error'=>'操作失败！该用户已加入此星球！'],400);
        }
        if(strtolower($data['mark']) === 'true') {
            $field = array(
                'group_base_id' => $info['group_base_id'],
                'user_base_id'  => $info['user_apply_id'],
                'authorization' => "03",
            );
            $this->Group_model->join_group($field);//将用户id加入对应的私密星球
            $m_type = 1;
            $msg = '操作成功！您已同意该成员的申请！';
            $status = 2;
        }else{
            $m_type = 2;
            $msg = '操作成功！您已拒绝该成员的申请！';
            $status = 3;
        }
        $this->process_app_info($m_type,$info);//加入私密星球的申请的结果返回给申请者
        $this->alter_status(['id'=>$data['m_id']],$status,'message_apply');//将消息列表已读转化为处理之后的标记(已同意或者已拒绝)
        $this->response(['success'=>$msg]);

//                $re=$this->Common_model->judgeUserOnline($info['user_apply_id']);
//                if(empty($re)){
//                    $rs['code']=2;
//                }
//                调用前端接口
    }

    /**
     * 加入私密星球的申请的结果返回给申请者
     * @param $m_type 1已同意 2已拒绝
     * @param $data mixed 申请信息相关数据
     */
    private function process_app_info($m_type,$data){
        $model = $this->User_model;
        $array = array(
            'user_base_id' => $data['user_apply_id'],
            'group_base_id' => $data['group_base_id'],
            'user_notice_id' => $data['user_base_id'],
            'create_time' => time(),
            'status' => 0,
            'type' =>$m_type
        );
        $model->process_app_info($array);
    }

    /**
     * 通过消息id获取私密星球申请消息详情
     * @param $m_id
     * @return mixed
     */
    private function get_message_info($m_id){
        $model = $this->User_model;
        return $model->get_message_info($m_id);
    }

    /**
     * 检测是否有新消息
     * @param $user_id
     */
    public function check_info_get($user_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data['user_id'] = $user_id;
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //获得新消息数目
        $num = $this->User_model->check_new_info($data['user_id']);
        $num_all = $num[0]+$num[1]+$num[2];
        if($num_all){
            $this->response(['success'=>TRUE]);
        }else{
            $this->response(['success'=>FALSE]);
        }
    }

    /**
     * 删除帖子通知中回复被删除或帖子不存在的帖子通知消息
     * @param $id
     * @param $mid
     */
    public function message_delete($id,$mid){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data = [
            'user_id' =>$id,
            'id'    =>$mid
        ];
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('delete_message') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断是否是用户帖子
        $message = $this->db->get_where('message_reply',['id'=>$mid])->row_array();
        $message['user_base_id'] == $id or $this->response(['error'=>'您无权删除他人的消息'],403);
        $message['status'] == 2 and $this->response(['error'=>'消息已被删除，无需再次操作'],409);

        //执行删除
        $rs = $this->alter_status($data,2,'message_reply');
        if($rs){
            $this->response(['success'=>'删除成功']);
        }else{
            $this->response(['error'=>'删除失败'],400);
        }
    }

    /**
     * 邮箱验证接口-用于发送包含验证邮箱验证码的邮件
     * @param $id
     */
    public function check_mail_post($id){
        //输入参数校验
        $data = array(
            'user_id' => $id,
            'num'        => 2,
            'token'     =>$this->input->get_request_header('Access-Token', TRUE)
            );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('check_token') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        //解析身份信息，判断是否发送邮件
        $token = $this->parsing_token($data['token']);
        if($token->user_id!=$id){
            $this->response(['error'=>'您没有权限'],403);
        }else{
            $data['email'] = $this->User_model->get_user_information($id)['email'];
            $this->send($data)?
                $this->response(NULL,204):
                $this->response(['error'=>'发送邮件失败'],400);
        }
    }

    /**
     * 解析jwt，获得用户id
     * @param $jwt
     * @return mixed
     */
    private function parsing_token($jwt)
    {
        try{
            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
            return $token;
        }
        catch(InvalidArgumentException $e)
        {
            return $this->response(['error'=>'身份信息已失效，请重新获取'],401);
        }
        catch(UnexpectedValueException $e)
        {
            return $this->response(['error'=>'身份信息已失效，请重新获取'],401);
        }
        catch(DomainException $e)
        {
            return $this->response(['error'=>'身份信息已失效，请重新获取'],401);
        }
    }

    /**
     * 邮箱验证接口-用于检验验证码的正确性并验证邮箱
     */
    public function check2mail_post(){
        if($this->post('token')){
            $token = $this->parsing_token($this->post('token'));
            $user_id = $token->user_id;
        }else{
            $user_id = NULL;
            $this->response(['error'=>'用户身份信息不能为空'],422);
        }
        $this->User_model->get_mail_checked($user_id) and $this->response(['error'=>'无需再次验证'],409);

        if($this->User_model->check_mail($user_id))
        {
            $this->response(['success'=>'验证成功']);
        }
        else
        {
            $this->response(['error'=>'验证失败'],400);
        }
    }

    /**
     * 生成用户token校验信息
     * @param $user_id
     * @param null $exp
     * @return string
     */
    private function get_user_token($user_id,$exp = NULL)
    {
        $exp = $exp?$exp:time()+600;
        $token = $this->jwt->encode(['exp'=>$exp,'user_id'=>$user_id],$this->config->item('encryption_key'));
        return $token;
    }

    /**
     * 找回密码的邮件内容
     * @param $data
     * @return string
     */
    private function email_psw($data){
        $this->load->library('jwt');
        $token = $this->get_user_token($data['user_id']);
        $url = DN.'retrievepassword/reset?token='.$token;
        $user_name = $data['user_name'];
        $time = date('Y-m-d H:i:s', time());
        $content ='亲爱的用户：'.$user_name.' 您好！<br>'
            .'您在'.$time.'提交了密码重置的请求。<br>'
            .'请在10分钟内点击下面的链接重设您的密码：<br><br>'
            ."请点击该验证码链接 <a href=$url>重置密码</a> (该链接在10分钟内有效)<br><br><br>"
            .'若您无法直接点击链接，也可以复制以下地址到浏览器地址栏中<br>'
            .$url;
        return $content;
    }

    /**
     * 验证邮箱的邮件内容
     * @param $data
     * @return string
     */
    private function email_check($data){
        $token = $this->get_user_token($data['user_id']);
        $url = DN.'users/verifyusermail?token='.$token;
        $user_name = $data['user_name'];
        $time = date('Y-m-d H:i:s', time());
        $content ='亲爱的用户：'.$user_name.' 您好！<br>'
            .'您在'.$time.'提交了邮箱验证的请求。<br>'
            .'请在10分钟内点击下面的链接验证您的邮箱：<br><br>'
            ."请点击该验证码链接 <a href=$url>验证邮箱</a> (该链接在10分钟内有效)<br><br><br>"
            .'若您无法直接点击链接，也可以复制以下地址到浏览器地址栏中<br>'
            .$url;
        return $content;
    }

    /**
     * 发送邮件
     * @param $data
     * @return bool
     */
    private function send($data) {

        $this->load->library('email');

        $email = $data['email'];
        $this->email->from('wuanlife@163.com','午安网团队');
        $this->email->to($email);
        if($data['num'] == 1)
        {
            $this->email->subject('午安网 - 找回密码');
            $content = $this->email_psw($data);
            $this->email->message($content);
        }
        else if($data['num'] == 2)
        {
            $this->email->subject('午安网 - 邮箱验证');
            $content = $this->email_check($data);
            $this->email->message($content);
        }
        if($this->email->send())
        {
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * 用于发送包含找回密码token的邮件
     */
    public function send_mail_post(){
         $data = array(
            'email'    => $this->post('mail'),
            'num'      => 1,
            );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('send_mail') == FALSE)
            $this->response(['error'=>validation_errors()],422);
        $sql = $this->User_model->user_email($data);
        if(empty($sql)){
            $this->response(['error'=>'您输入的账号不存在！'],422);
        }
        else
        {
            $data['user_id'] = $sql['id'];
            $data['user_name'] = $sql['nickname'];
            $this->send($data)?
                $this->response(NULL,204):
                $this->response(['error'=>'发送邮件失败'],400);
        }

    }

    /**
     * 校验用户token并找回密码
     */
    public function repassword_put(){
        //用户身份信息校验
        $jwt = $this->put('token');
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'token' =>$jwt,
            'password' =>$this->put('password'),
            'user_id' =>$token->user_id
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('re_psw') == FALSE){
            $this->response(['error'=>validation_errors()],422);
        }else{
            $data['password'] = md5($data['password']);
            $this->update_user_status($data['password'],$data['user_id'],TRUE);
            $this->User_model->re_psw($data)? //更新密码
                $this->response(['success'=>'重置密码成功！']):
                $this->response(['error'=>'重置失败，请重试'],400);
        }
    }

    /**
     * 修改密码接口
     * @param $user_id
     *
     */
    public function password_put($user_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data = array(
            'user_id'     => $user_id,
            'password'         => $this->put('password'),
            'psw'      => $this->put('psw'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('change_pwd') == FALSE)
            $this->response(['error'=>validation_errors()],422);


        $password = $this->User_model->get_user_information($data['user_id'])['password'];
        if(md5($data['password']) === $password){
            $data['password'] = md5($data['psw']);
            $this->update_user_status($data['psw'],$data['user_id'],TRUE);
            $this->User_model->re_psw($data)?
                $this->response(['success'=>'修改成功']):
                $this->response(['error'=>'修改失败'],400);
        }else{
            $this->response(['error'=>'原密码不正确，请重试'],403);
        }
    }

    /**
     * 获取用户邮箱验证状态，后续会移除这里的代码*2017/7/25 0025
     * 原接口已合并在获取用户信息接口，
     *
     */
    /**
 *    public function get_mail_checked(){
        $id = $this->get('user_id');
        $this->form_validation->set_data($_GET);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        $rs = $this->User_model->get_mail_checked($id);
        $re = [
            'user_id' =>(int)$id,
            'mail_check'=>0
        ];
        if($rs){
            $re = [
                'user_id' =>(int)$id,
                'mail_check'=>1
            ];
        }
        $this->response($re,200,NULL);
    }*/

}