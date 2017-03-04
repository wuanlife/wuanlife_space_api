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

    public function login($email,$password){
        $data=array(
            'email' => $email,
            'password' => $password,
        );
        $re['code']=0;
        $model= $this->User_model->login($data);
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


    public function reg(){
/*        $data=array(
            'nickname'=>$nickname,
            'email'=>$email,
            'password'=>$password,
        );*/
        if(true){
          //  $this->response(1,200,1);
        }
        if(true){
            $this->response(2,200,2);
        }
    }
























}