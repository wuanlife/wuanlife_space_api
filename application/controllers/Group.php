<?php



class Group extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Group_model');
        $this->load->model('Common_model');
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
     * 星球创建接口
     * @desc 用于创建星球
     * @return int code 操作码，1表示创建成功，0表示创建失败
     * @return object info 星球信息对象
     * @return int info.group_base_id 星球ID
     * @return string info.user_base_id 创建者ID
     * @return string info.authorization 权限，01表示创建者
     * @return string info.name 星球名称
     * @return string msg 提示信息
     */
    public function create($user_id,$g_name,$private,$g_image=null,$g_introduction=null){
        $private=$this->Common_model->judgePrivate($private);
        $data=array(
            'user_id'=>$user_id,
            'g_name'=>$g_name,
            'g_image'=>$g_image,
            'g_introduction'=>$g_introduction,
            'private'=>$private,
        );
        $msg=null;
        $check_group_name=$this->check_group_name($g_name);
        if($check_group_name['code']){
            if(empty($g_image)){
                $data['g_image']='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
            $create=$this->Group_model->create($data);
            if($create){
                $re['code']=1;
                $msg='创建成功！';
            }else{
                $re['code']=0;
                $msg='创建失败';
            }
        }else{
            $msg=$check_group_name['msg'];
            $re['code']=$check_group_name['code'];
        }

        $this->response($re,200,$msg);
    }

    /**
     * 加入星球接口
     * @desc 用户加入星球
     * @return int code 操作码，1表示加入成功，0表示加入失败
     * @return object info 星球信息对象
     * @return int info.group_base_id 加入星球ID
     * @return string info.user_base_id 加入者ID
     * @return string info.authorization 权限，03表示会员
     * @return string msg 提示信息
     */
    public function join($user_id,$g_id){
        $data = array(
            'user_base_id' => $user_id,
            'group_base_id'    => $g_id,
            'authorization'=>'03',
        );
        $re=$this->Group_model->join($data);
        if($re){
            $msg='加入成功！并通知星球创建者';
            $rs['code']=1;
        }else{
            $msg='加入失败';
            $rs['code']=0;
        }
        $this->response($re,200,$msg);
    }


    /**
     * 退出星球接口
     * @desc 用户退出星球
     * @return int code 操作码，1表示退出成功，0表示退出失败
     * @return string msg 提示信息
     */
    public function quit($user_id,$g_id){
        $data=array(
            'user_id'=>$user_id,
            'group_id'=>$g_id,
        );
        $creator=$this->Group_model->judge_group_creator($data);
        if($creator){
            $msg='您是星球创建者，无法退出';
            $re['code']=0;
        }else{
            $this->Group_model->quit($data);
            $re['code']=1;
            $msg='退出成功！并通知星球创建者';
        }
        $this->response($re,200,$msg);
    }




    /**
     * 判断用户是否加入该星球
     * @desc 判断用户是否加入该星球
     * @return int code 操作码，1表示已加入，0表示未加入
     * @return string msg 提示信息
     */
    public function g_status($user_id,$g_id){
        $data=array(
            'user_id'=>$user_id,
            'g_id'=>$g_id,
        );
        $re['code']=$this->Group_model->g_status($data);
        if($re['code']) {
            $msg = '已加入该星球';
        }else{
            $msg='未加入该星球';
        }
        $this->response($re,200,$msg);
    }



    public function get_group_info($group_id,$user_id){
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$user_id,
        );
        $group_exist=$this->Group_model->judge_group_exist($group_id);
        $creator=$this->Group_model->judge_group_creator($data);
        if($group_exist){
            $re=$this->Group_model->get_group_info($group_id);
            if($creator){
                $re['creator']=1;
            }else{
                $re['creator']=0;
            }
        }else{
            $re=0;
        }
        $this->response($re,200,null);
    }

    /**
     *修改星球接口
     * @desc 修改星球详情
     * @return int data 0代表修改失败,1代表修改成功
     * @return string msg 提示错误信息
     */
    public function alter_group_info($group_id,$user_id,$g_introduction=null,$g_image=null){
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$user_id,
            'g_introduction'=>$g_introduction,
            'g_image'=>$g_image,
        );
        $group_exist=$this->Group_model->judge_group_exist($group_id);
        $creator=$this->Group_model->judge_group_creator($data);
        if($group_exist){
            if($creator){
                $this->Group_model->alter_group_info($data);
                $re['code']=1;
                $msg='修改成功';
            }else{
                $re['code']=0;
                $msg='不是创建者';
            }
        }else{
            $re['code']=0;
            $msg='星球不存在';
        }
        $this->response($re,200,$msg);
    }







    /*
     * 判断星球名称是否合法
     */
    public function check_group_name($g_name){
        $re=$this->Group_model->gname_exist($g_name);
        $rs['code']=0;
        if (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u', $g_name)) {
            $rs['msg'] = '小组名只能为中文、英文、数字或者下划线，但不得超过20字节！';
        }elseif (!empty($re)) {
            $rs['msg'] = '该星球已创建！';
        }else{
            $rs['code']=1;
        }
        return $rs;
    }





}