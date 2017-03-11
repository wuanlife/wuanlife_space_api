<?php



class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Group_model');
        $this->load->model('Common_model');
        $this->load->helper('url_helper');
    }
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
    public function login($email=null,$password=null){
        $data=array(
            'email' => $email,
            'password' => $password,
        );
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
    public function reg($nickname,$email,$password){
        $data=array(
            'nickname'=>$nickname,
            'email'=>$email,
            'password'=>$password,
        );
        $re['code']=0;
        $user_email=$this->User_model->user_email($data);
        $user_nickname=$this->User_model->user_nickname($data);
        if(!empty($user_email)){
            $msg='该邮箱已注册！';
        }elseif (!empty($user_nickname)){
            $msg='该昵称已注册！';
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
    public function get_user_info($user_id){
        $re=$this->User_model->get_user_info($user_id);
        $msg=null;
        $this->response($re,200,$msg);

    }


    /**
     *修改用户信息接口
     * @desc 修改用户的信息
     * @return int data true代表成功修改，false代表修改失败
     */
    public function alter_user_info($user_id,$user_name=null,$profile_picture=null,$sex=null,$year=null,$month=null,$day=null){
        $data = array(
            'user_id'       =>$user_id,
            'nickname'      =>$user_name,
            'profile_picture'   =>$profile_picture,
            'sex'           =>$sex,
            'year'          =>$year,
            'month'         =>$month,
            'day'           =>$day,
        );
        $judge_name=$this->User_model->user_nickname($data);
        if($judge_name){
            $msg='用户名被占用';
            $this->response(false,200,$msg);
        }
        $re=$this->User_model->alter_user_info($data);
        $msg=null;
        $this->response($re,200,$msg);
    }
    public function show_message(){
        $data = array(
            'user_id'       => $this->input->get('user_id'),
            'm_type'   => $this->input->get('m_type'),
            'pn'            => $this->input->get('pn'),
        );
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
            $re['pageCount']  = $rs['pageCount'];
            $re['currentPage']  = (int)$rs['currentPage'];
            $msg  = '接收成功';
        }else{
            $re['code'] = 0;
            $msg  = '您暂时没有消息！';
        }
        $this->response($re,200,$msg);
    }

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
        $rs = array();
        if($data['pn'] !=0){
            $rs['info'] = $model->show_reply_message($data,$page_num);
        }
        $rs['pageCount']  = $pageCount;
        $rs['currentPage'] = $data['pn'];
        return $rs;
    }

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
            $re['info'] = $model->show_apply_message($data,$page_num);
        }
        $re['pageCount']  = $pageCount;
        $re['currentPage'] = $data['pn'];
        return $re;
    }
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
            $re['info'] = $model->show_notice_message($data,$page_num);
            foreach($re['info'] as $keys => $values){
                $re['info'][$keys]['content'] = $this->message_array($values['type'],$values['user_name'],$values['g_name']);
                if(in_array($values['type'],array(4,5))){
                    $re['info'][$keys]['image'] = $model->get_user_information($values['user_id'])['profile_picture'];
                }elseif(in_array($values['type'],array(1,2,3))){
                    $re['info'][$keys]['image'] = $this->Group_model->get_group_infomation($values['group_id'])['g_image'];
                }
            }
        }
        $re['pageCount']  = $pageCount;
        $re['currentPage'] = $data['pn'];
        return $re;
    }
    private function get_index_message($data){
        error_reporting(0);
        $model = $this->User_model;
        $rs['pageCount']  = 1;
        $rs['currentPage'] = 1;
        $rs['info'] =array(
            'notice'=>$model->show_notice_message($data,20)[0] ,
            'apply'=>$model->show_apply_message($data,20)[0] ,
            'reply'=>$model->show_reply_message($data,20)[0]
        );
        return $rs;
    }

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

    private function alter_status($value,$status,$table){
        $model = $this->User_model;
        return $model->alter_status($value,$status,$table);
    }
    public function process_apply(){
        $data = array(
            'user_id'       => $this->input->get('user_id'),
            'm_id'   => $this->input->get('m_id'),
            'mark'            => $this->input->get('mark'),
        );
        $rs = $this->process_apply_1($data);
        $this->response($rs,200,$msg=NULL);
    }
    private function process_apply_1($data){
        $info = $this->get_message_info($data['m_id']);
        $founder_id = $this->Group_model->get_group_infomation($info['group_base_id'])['user_base_id'];
        if($founder_id==$data['user_id']){
            if($this->Common_model->check_group($info['user_apply_id'],$info['group_base_id'])){
                $rs['msg'] = '操作失败！该用户已加入此星球！';
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
                    $rs['msg'] = '操作成功！您已同意该成员的申请！';
                    $status = 2;
                }else {
                    $rs['msg'] = '操作成功！您已拒绝该成员的申请！';
                    $status = 3;
                }
                $values['id'] = $data['m_id'];
                $table = 'message_apply';
                $this->alter_status($values,$status,$table);//将消息列表已读转化为处理之后的标记(已同意或者已拒绝)
            }
        }else{
            $rs['msg'] = '您不是创建者，没有权限！';
        }
        /*
        $re=$this->Common_model->judgeUserOnline($info['user_apply_id']);
        if(empty($re)){
            $rs['code']=2;
        }
        调用前端接口
        */
        return $rs;
    }
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
    private function get_message_info($m_id){
        $model = $this->User_model;
        return $model->get_message_info($m_id);
    }
    public function check_new_info(){
        $id = $this->input->get('user_id');
        $model = $this->User_model;
        $num = $model->check_new_info($id);
        $num_all = $num[0]+$num[1]+$num[2];
        if($num_all){
            $rs['num']=1;
        }else{
            $rs['num']=0;
        }
        $this->response($rs,200,$msg=NULL);
    }
    public function delete_message(){
        $data['id'] = $this->input->get('m_id');
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
    public function test(){
        $data = array(
            'id'=>'',
            'name'=>'csc',
            'psw'=>''
        );
        $this->load->library('Validator');
        $rs = $this->validator->check($data,__FUNCTION__);
        $this->response($rs);

    }


    private function email(){
        $this->load->library('email');
        $this->email->from('wuanlife@163.com', '陈世超');
        $this->email->to('1195417752@qq.com');
        $this->email->subject('ssl模式发送');
        $this->email->message('Testing s.');
        var_dump($this->email->send());
    }
    private function verification_code(){
        $this->load->helper('icode');
        $cap = create_code(5,'123456789');
        echo $cap;

    }
}