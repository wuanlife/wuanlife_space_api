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
























}