<?php



class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper('url_helper');
    }
    public function response($data,$ret=200,$msg=null){
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
        }elseif($data['password']!=$model['password']){
            $msg='密码错误，请重试！';
        }else{
            $re['info']= array('userID' => $model['id'], 'nickname' => $model['nickname'], 'Email' => $model['email']);
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
    public function getUserInfo($user_id){
        $re=$this->User_model->getUserInfo($user_id);
        $msg=null;
        $this->response($re,200,$msg);

    }


    /**
     *修改用户信息接口
     * @desc 修改用户的信息
     * @return int data true代表成功修改，false代表修改失败
     */
    public function alterUserInfo($user_id,$user_name=null,$profile_picture=null,$sex=null,$year=null,$month=null,$day=null){
        $data = array(
            'user_id'       =>$user_id,
            'nickname'      =>$user_name,
            'profile_picture'   =>$profile_picture,
            'sex'           =>$sex,
            'year'          =>$year,
            'month'         =>$month,
            'day'           =>$day,
        );
        $re=$this->User_model->alterUserInfo($data);
        $msg=null;
        $this->response($re,200,$msg);
    }














}