<?php


class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url','url_helper'));
        $this->load->library(array('form_validation','jwt'));
        $this->load->model(array('User_model','Group_model','Common_model'));
        $this->form_validation->set_message('required', '{field} 参数是必填选项.');
        $this->form_validation->set_message('min_length', '{field} 参数长度不小于{param}.');
        $this->form_validation->set_message('max_length', '{field} 参数长度不大于{param}.');
        $this->form_validation->set_message('valid_email', '{field} 参数不是合法邮箱地址.');

    }
	public function index(){
		echo '接口测试<br>登录接口url：<br>';
		echo 'index.php/user/login';
	}
    /**
     * @param $data
     * @param int $ret
     * @param null $msg
     * 返回JSON数据到前端
     */
    private function response($data,$ret=200,$msg=null){
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
    }


    /**
     * 登录接口
     * @desc 用于验证并登录用户
     * @return int code 操作码，1表示登录成功，0表示登录失败
     * @return object info 用户信息对象
     * @return int info.id 用户ID
     * @return string info.nickname 用户昵称
     * @return string msg 提示信息
     *
     */
    public function login(){
        $data=array(
            'email' => $this->input->post('user_email'),
            'password' => $this->input->post('password'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('login') == FALSE)
            $this->response(null,400,validation_errors());
        $re['code']=0;
        $model= $this->User_model->user_email($data);
        if(!$model){
            $msg='该邮箱尚未注册！';
        }elseif(md5($data['password'])!=$model['password']){
            $msg='密码错误，请重试！';
        }else{
            $re['info']= array('user_id' => $model['id'], 'user_name' => $model['nickname'], 'user_email' => $model['email']);
            $re['code']='1';
            $msg='登录成功！';
        }
        $this->response($re,200,$msg);
    }


    /**
     * 注册接口
     * @desc 用于验证并注册用户
     * @return int code 操作码，1表示注册成功，0表示注册失败
     * @return object info 用户信息对象
     * @return int info.id 用户ID
     * @return string info.nickname 用户昵称
     * @return string msg 提示信息
     *
     */
    public function show_code(){
        $data['user_id'] = $this->input->post('user_id');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        $rs = $this->User_model->show_code($data['user_id']);
        if(empty($rs)){
            $this->load->helper('icode');
            $i_code = create_code(6);
            $this->User_model->create_code($data['user_id'],$i_code);
            $rs = $this->User_model->show_code($data['user_id']);
        }
        $this->response($rs,200,'查询成功');
    }
    public function reg(){
        $data=array(
            'nickname'=>$this->input->post('user_name'),
            'email'=>$this->input->post('user_email'),
            'password'=>$this->input->post('password'),
            'code' =>$this->input->post('i_code'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('reg') == FALSE)
            $this->response(null,400,validation_errors());
        $re['code']=0;
        $user_email=$this->User_model->user_email($data);
        $user_nickname=$this->User_model->user_nickname($data);
        $invite_code = $this->User_model->invite_code($data['code']);
        if(!empty($user_email)){
            $msg='该邮箱已注册！';
        }elseif (!empty($user_nickname)){
            $msg='该昵称已注册！';
        }elseif(empty($invite_code)||$invite_code['used']==0){
            $msg='邀请码已过期或不存在！';
        }else{
            $re['info']=$this->User_model->reg($data);
            $re['code']=1;
            $msg='注册成功，并自动登录！';
        }
        $this->response($re,200,$msg);
    }
        /**
     * 注销接口
     * @desc 用于清除用户登录状态
     * @return int code 操作码，1表示注销成功，0表示注销失败
     * @return string msg 提示信息
     */
    public function logout(){
        $re['code']='1';
        $msg='注销成功！';
        $this->response($re,200,$msg);
    }


    /**
     *获取用户信息
     * @desc 用于获取用户的信息
     * @return int userID 用户id
     * @return string Email 用户Email
     * @return string nickname 用户名称
     * @return int sex 用户性别,0为未设，1为男，2为女
     * @return string year 年
     * @return string month 月
     * @return string day 日
     * @return string mailChecked 是否验证邮箱，0为未验证邮箱，1为已验证邮箱
     */
    public function get_user_info(){
        $user_id=$this->input->get('user_id');
        $re=$this->User_model->get_user_info($user_id);
        $msg=null;
        $this->response($re,200,$msg);

    }


    /**
     *修改用户信息接口
     * @desc 修改用户的信息
     * @return int data true代表成功修改，false代表修改失败
     */
    public function alter_user_info(){
        $data = array(
            'user_id'       =>$this->input->get('user_id'),
            'nickname'      =>$this->input->get('user_name'),
            'profile_picture'   =>$this->input->get('profile_picture'),
            'sex'           =>$this->input->get('sex'),
            'year'          =>$this->input->get('year'),
            'month'         =>$this->input->get('month'),
            'day'           =>$this->input->get('day'),
        );
        $re=$this->User_model->alter_user_info($data);
        $this->response($re['code'],200,$re['msg']);
    }
    /**
     * 用户消息列表展示
     * m_type 1帖子通知 2星球通知 3私密星球申请 4消息列表主页
     */
    public function show_message(){
        $data = array(
            'user_id'       => $this->input->post('user_id'),
            'm_type'   => $this->input->post('m_type'),
            'pn'            => $this->input->post('pn'),
        );
        $data['m_type'] = $data['m_type']?$data['m_type']:4;
        $data['pn'] = $data['pn']?$data['pn']:1;
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        if($data['m_type']==1){
            $rs = $this->get_reply_message($data);
        }elseif($data['m_type']==2){
            $rs = $this->get_notice_message($data);
        }elseif($data['m_type']==3){
            $rs = $this->get_apply_message($data);
        }elseif($data['m_type']==4){
            $rs = $this->get_index_message($data);
        }
        if(!empty($rs['info'])){
            $re['code'] = 1;
            $re['info'] = $rs['info'];
            $re['page_count']  = $rs['pageCount'];
            $re['current_page']  = (int)$rs['currentPage'];
            $msg  = '接收成功';
        }else{
            $re['code'] = 0;
            $msg  = '您暂时没有消息！';
        }
        $this->response($re,200,$msg);
    }

    /**
     * @param $data
     * @return array
     * 获取用户消息列表，帖子通知
     */
    private function get_reply_message($data){
        $model= $this->User_model;
        $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
        $status = 1;//消息已读
        $table = 'message_reply';
        $this->alter_status($value,$status,$table);//将消息列表转化为已读
        $num = $model->get_num_message($data['user_id'],$table);
        //$num = $model->get_num_reply_message($data['user_id']);
        $page_num = 20;
        $pageCount  = ceil($num/$page_num);
        if($data['pn'] > $pageCount){
            $data['pn'] = $pageCount;
        }
        $re = array();
        if($data['pn'] !=0){
            $rs['info'] = $model->show_reply_message($data,$page_num);
            foreach($rs['info'] as $key=>$value){
                //没有头像给默认头像
                if(empty($value['profile_picture'])){
                    $value['profile_picture'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
                }
                $re['info'][$key]['users']=array(
                    'user_id'=>$value['user_id'],
                    'user_name'=>$value['user_name'],
                );
                $re['info'][$key]['posts']=array(
                    'post_id'=>$value['post_id'],
                    'p_title'=>$value['p_title'],
                    'reply_floor'=>$value['reply_floor'],
                    'page'=>$this->Common_model->get_post_reply_page($value['post_id'],$value['reply_floor']),
                );
                $re['info'][$key]['messages']=array(
                    'm_id'=>$value['m_id'],
                    'image'=>$value['profile_picture'],
                );
            }
        }
        $re['pageCount']  = $pageCount;
        $re['currentPage'] = $data['pn'];
        return $re;
    }

    /**
     * @param $data
     * @return array
     * 获取用户消息列表，私密星球申请
     */
    private function get_apply_message($data){
        $model = $this->User_model;
        //$num = $model->get_num_apply_message($data['user_id']);
        $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
        $status = 1;//消息已读
        $table = 'message_apply';
        $this->alter_status($value,$status,$table);//将消息列表转化为已读
        $num = $model->get_num_message($data['user_id'],$table);
        $page_num = 20;
        $pageCount  = ceil($num/$page_num);
        if($data['pn'] > $pageCount){
            $data['pn'] = $pageCount;
        }
        $re = array();
        if($data['pn'] !=0){
            $rs['info'] = $model->show_apply_message($data,$page_num);
            foreach($rs['info'] as $key=>$value){
                //没有头像给默认头像
                if(empty($value['profile_picture'])){
                    $value['profile_picture'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
                }
                $re['info'][$key]['users']=array(
                    'user_id'=>$value['user_id'],
                    'user_name'=>$value['user_name'],
                );
                $re['info'][$key]['groups']=array(
                    'group_id'=>$value['group_id'],
                    'g_name'=>$value['g_name'],
                );
                $re['info'][$key]['messages']=array(
                    'm_id'=>$value['m_id'],
                    'status'=>$value['status'],
                    'image'=>$value['profile_picture'],
                    'text'=>$value['text'],
                );
            }
        }
        $re['pageCount']  = $pageCount;
        $re['currentPage'] = $data['pn'];
        return $re;
    }
    /**
     * @param $data
     * @return array
     * 获取用户消息列表，星球通知
     */
    private function get_notice_message($data){
        $model = $this->User_model;
        $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
        $status = 1;//消息已读
        $table = 'message_notice';
        $this->alter_status($value,$status,$table);//将消息列表转化为已读
        $num = $model->get_num_message($data['user_id'],$table);
        $page_num = 20;
        $pageCount  = ceil($num/$page_num);
        if($data['pn'] > $pageCount){
            $data['pn'] = $pageCount;
        }
        $re = array();
        if($data['pn'] !=0){
            $rs['info'] = $model->show_notice_message($data,$page_num);
            foreach($rs['info'] as $key => $value){
                $re['info'][$key]['content'] = $this->message_array($value['type'],$value['user_name'],$value['g_name']);
                if(in_array($value['type'],array(4,5))){
                    $image = $model->get_user_information($value['user_id'])['profile_picture'];
                }elseif(in_array($value['type'],array(1,2,3))){
                    $image = $this->Group_model->get_group_infomation($value['group_id'])['g_image'];
                }
                //没有头像给默认头像
                if(empty($image)){
                    $image = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
                }
                $re['info'][$key]['users']=array(
                    'user_id'=>$value['user_id'],
                    'user_name'=>$value['user_name'],
                );
                $re['info'][$key]['groups']=array(
                    'group_id'=>$value['group_id'],
                    'g_name'=>$value['g_name'],
                );
                $re['info'][$key]['messages']=array(
                    'm_id'=>$value['m_id'],
                    'type'=>$value['type'],
                    'status'=>$value['status'],
                    'image'=>$image,
                );
            }
        }
        $re['pageCount']  = $pageCount;
        $re['currentPage'] = $data['pn'];
        return $re;
    }
    /**
     * @param $data
     * @return mixed
     * 获取用户消息列表，主页
     */
    private function get_index_message($data){
        $data['pn'] = empty($data['pn'])?1:$data['pn'];
        error_reporting(0);
        $rs['pageCount']  = 1;
        $rs['currentPage'] = 1;
        $rs['info'] =array(
            'notice'=>$this->get_notice_message($data)['info'][0] ,
            'apply'=>$this->get_apply_message($data)['info'][0] ,
            'reply'=>$this->get_reply_message($data)['info'][0] ,
        );
        return $rs;
    }

    /**
     * @param $type 1同意加入 2拒绝加入 3移出星球 4退出星球 5加入星球
     * @param $user_name
     * @param $g_name
     * @return mixed
     * 获取用户星球通知不同类型的拼接消息
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
     * @param $value
     * @param $status int 0未读 1已读；0未处理 1已同意 2已拒绝
     * @param $table string 数据表名称
     * @return CI_DB_active_record
     * 修改消息的状态
     */
    private function alter_status($value,$status,$table){
        $model = $this->User_model;
        return $model->alter_status($value,$status,$table);
    }
    /**
     * 申请加入私密星球
     */
    public function process_apply(){
        $data = array(
            'user_id'       => $this->input->get('user_id'),
            'm_id'   => $this->input->get('m_id'),
            'mark'            => $this->input->get('mark'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('process_apply') == FALSE)
            $this->response(null,400,validation_errors());
        $rs['code'] = 0;
        $info = $this->get_message_info($data['m_id']);
        $founder_id = $this->Group_model->get_group_infomation($info['group_base_id'])['user_base_id'];
        if($founder_id==$data['user_id']){
            if($this->Common_model->check_group($info['user_apply_id'],$info['group_base_id'])){
                $msg = '操作失败！该用户已加入此星球！';
                $status = 2;
            }else{
                if($data['mark'] == 1) {
                    $field = array(
                        'group_base_id' => $info['group_base_id'],
                        'user_base_id'  => $info['user_apply_id'],
                        'authorization' => "03",
                    );
                    $m_type = 1;
                    $model_g = $this->Group_model;
                    $model_g->join($field);//将用户id加入对应的私密星球
                }else{
                    $m_type = 2;
                }
                $this->process_app_info($m_type,$info);//加入私密星球的申请的结果返回给申请者
                $rs['code'] = 1;
                if($data['mark'] == 1) {
                    $msg = '操作成功！您已同意该成员的申请！';
                    $status = 2;
                }else {
                    $msg = '操作成功！您已拒绝该成员的申请！';
                    $status = 3;
                }
            }
                $values['id'] = $data['m_id'];
                $table = 'message_apply';
                $this->alter_status($values,$status,$table);//将消息列表已读转化为处理之后的标记(已同意或者已拒绝)
        }else{
            $msg = '您不是创建者，没有权限！';
        }
        /*
        $re=$this->Common_model->judgeUserOnline($info['user_apply_id']);
        if(empty($re)){
            $rs['code']=2;
        }
        调用前端接口
        */
        $this->response($rs,200,$msg);
    }
    /**
     * @param $m_type 1已同意 2已拒绝
     * @param $data mixed 申请信息相关数据
     * 加入私密星球的申请的结果返回给申请者
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
     * @param $m_id
     * @return mixed
     * 通过消息id获取私密星球申请消息详情
     */
    private function get_message_info($m_id){
        $model = $this->User_model;
        return $model->get_message_info($m_id);
    }
    /**
     * 检测是否有新消息
     */
    public function check_new_info(){
        $data['user_id'] = $this->input->get('user_id');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        $model = $this->User_model;
        $num = $model->check_new_info($data['user_id']);
        $num_all = $num[0]+$num[1]+$num[2];
        if($num_all){
            $rs['num']=1;
        }else{
            $rs['num']=0;
        }
        $this->response($rs,200,$msg=NULL);
    }
    /**
     * 删除帖子通知中回复被删除或帖子不存在的帖子通知消息
     */
    public function delete_message(){
        $data['id'] = $this->input->get('m_id');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('delete_message') == FALSE)
            $this->response(null,400,validation_errors());
        $rs = $this->alter_status($data,2,'message_reply');
        if($rs){
            $msg = '删除成功';
            $re['code'] = 1;
        }else{
            $msg = '删除失败';
            $re['code'] = 0;
        }
        $this->response($re,200,$msg);
    }

    /**
     * 测试接口，待完成所有接口之后删除
     */
    public function test()
    {
        /**
         * 发送邮件测试接口，配置信息保存在config/email.php
         */
        $this->load->library('email');
        $this->email->from('wuanlife@163.com', 'xiaochao');
        $this->email->to('1195417752@qq.com');
        $this->email->subject('ssl模式发送');
        $this->email->message('Testing s.');
        var_dump($this->email->send());
        /**
         * 验证码生成测试接口
         */
        $this->load->helper('icode');
        $cap = create_code(5,'123456789');
        echo $cap;

    }


    /**
     * 邮箱验证接口-用于发送包含验证邮箱验证码的邮件
     */

    public function check_mail_1(){
        $data = array(
            'email' => $this->input->get('user_email'),
            'num'        => 2,
            );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('send_mail') == FALSE)
            $this->response(null,400,validation_errors());
        $sql = $this->User_model->user_email($data);
        if(empty($sql)){
            $re['code'] = 0;
            $re['msg'] = '您输入的账号不存在！';
        }
        else
        {
            $data['user_id'] = $sql['id'];
            $data['user_name'] = $sql['nickname'];
            $re = $this->send($data);
        }
        $this->response(['code'=>$re['code']],200,$re['msg']);
    }
/**
 * 邮箱验证接口-用于检验验证码的正确性并验证邮箱
 */
    public function check_mail_2(){
        $data = array(
            'user_id' => $this->input->get('user_id'),
            );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        if($this->User_model->check_mail($data))
        {
            $re['code'] = 1;
            $msg = "验证成功";
        }
        else
        {
            $re['code'] = 0;
            $msg = "验证失败";
        }

        $this->response($re,200,$msg);
    }

    /**
     * @param $data
     * @return string
     */
    private function email_psw($data){
        $this->load->library('jwt');
        $token = $this->jwt->encode(['exp'=>time()+600,'user_id'=>$data['user_id']]);
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
    private function email_check($data){
        $token = $this->jwt->encode(['exp'=>time()+600,'user_id'=>$data['user_id']]);
        $url = DN.'verifyemail?token='.$token;
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
            $re['code'] = 1;
            $re['msg'] = "发送成功";
        }else{
            $re['code'] = 0;
            $re['msg'] = "发送失败";
        }
        return $re;
    }

    /**
     * 用于发送包含修改密码token的邮件
     */
    public function send_mail(){
         $data = array(
            'email'    => $this->input->get('user_email'),
            'num'      => 1,
            );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('send_mail') == FALSE)
            $this->response(null,400,validation_errors());
        $sql = $this->User_model->user_email($data);
        if(empty($sql)){
            $re['code'] = 0;
            $re['msg'] = '您输入的账号不存在！';
        }
        else
        {
            $data['user_id'] = $sql['id'];
            $data['user_name'] = $sql['nickname'];
            $re = $this->send($data);
        }
        $this->response(['code'=>$re['code']],200,$re['msg']);
    }

    /**
     *
     */
    public function re_psw(){
        $data = array(
            'user_id' =>$this->input->post('user_id'),
            'password'=>$this->input->post('password'),
            'psw'     =>$this->input->post('psw'),
            );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('re_psw') == FALSE)
            $this->response(null,400,validation_errors());
        $re['code'] = 0;
        if($data['password']==$data['psw']){
            $data['password'] = md5($data['password']);
            $this->User_model->re_psw($data); //更新密码
            $msg = '密码修改成功！';
            $re['code'] = 1;
        }else{
            $re['code'] = 0;
            $msg = '两次密码不一致，请确认！';
        }
        $this->response($re,200,$msg);

    }        
    

    public function change_pwd(){
        $data = array(
            'user_id'     => $this->input->get('user_id'),
            'pwd'         => $this->input->get('pwd'),
            'new_pwd'      => $this->input->get('new_pwd'),
            'check_new_pwd' => $this->input->get('check_new_pwd'),
        );
        if($this->User_model->get_user_information($data['user_id']))
        {
            $info = $this->User_model->get_user_information($data['user_id']);
            //print_r($info);
            if(md5($data['pwd']) == $info['password'])
            {
                if($data['new_pwd'] ==$data['check_new_pwd'])
                {
                    $data['user_email'] = $info['email'];
                    $data['password'] = $data['new_pwd'];
                    $data['password'] = md5($data['password']);
                    $this->User_model->re_psw($data);
                    $msg ='修改成功';
                }
                else
                {
                    $re['code'] = 0;
                    $msg = "两次密码不一致，请确认！";
                }
            }
            else
            {
                $re['code'] = 0;
                $msg = "密码错误，请重试！";
            }
            $re['code'] = 1;
            $this->response($re,200,$msg);
        }
    }

    public function get_mail_checked(){
        $id = $this->input->get('user_id');
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
    }

}