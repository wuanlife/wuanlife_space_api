<?php


/**
 * @property User_model      $User_model
 * @property Group_model    $Group_model
 * @property Post_model     $Post_model
 *
 */
class Common_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->model('User_model');
        $this->load->model('Group_model');
        $this->load->model('Post_model');
        //$model_self = & get_instance();
    }


    /**
      * 判断是否为私密
      */
    public function judgePrivate($private){
        if($private==1){
            $private=1;
        }else{
            $private=0;
        }

        return $private;
    }

    /*
     *判断用户是否在线(调用前端接口)
     */
    /**
     * 先被注释  待修改
     *
     * public function judgeUserOnline($user_id){
     * $data1 = array ('userid' => $user_id);
     * $data1 = http_build_query($data1);
     * $RootDIR = dirname(__FILE__);
     * $path=$RootDIR."/../../Public/init.php";
     * require_once $path;
     * //$url=DI()->config->get('sys.url');
     *
     *
     * $opts = array (
     * 'http' => array (
     * 'method' => 'POST',
     * 'header'=> "Content-type: application/x-www-form-urlencoded",
     * 'content' => $data1
     * )
     * );
     * $context = stream_context_create($opts);
     * $html = file_get_contents($url, false, $context);
     * return $html;
     * }
     * /
     * /*
     * 判断星球是否有头像，若没有给默认头像
     * @param $lists
     * @return
     */
    public function judge_image_exist($lists){
        for($i=0;$i<count($lists);$i++){
            if(empty($lists[$i]["g_image"])||$lists[$i]["g_image"]==null){
                $lists[$i]["g_image"]='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
        }
        return $lists;
    }

    /**
     * @param $group_id
     * @return int
     * 判断星球的私密性
     */
    public function judge_group_private($group_id){
        $re =$this->Group_model->get_group_infomation($group_id)['private'];
        return $re;
    }
    /*
     * @param $group_id
     * @return int
     * 已存在相同函数
    public function judge_group_exist($group_id){
        $sql=$this->db->select('id')
            ->where('id',$group_id)
            ->where('`delete`','0')
            ->get('group_base')
            ->row_array();
        if(!empty($sql)){
            $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }
     * /

    /**
     * @param $post_id
     * @param $user_id
     * @return int
     * 判断用户是否收藏帖子
     */
    public function judge_collect_post($post_id,$user_id){
        $re=$this->Post_model->judge_collect_post($post_id,$user_id);
        return $re;
    }

    /**
     * @param $group_id
     * @return int
     * 判断星球是否存在
     */
    public function judge_group_exist($group_id){
        $re=$this->Group_model->get_group_infomation($group_id)['delete'];
        return !$re;
    }

    /**
     * @param $post_id
     * @return int
     * 判断帖子是否存在
     */
    public function judge_post_exist($post_id){
        $group_id = $this->Post_model->get_post_information($post_id)['group_base_id'];
        $rs=$this->judge_group_exist($group_id);
        if($rs){
            $sql=!$this->Post_model->get_post_information($post_id)['delete'];
        }
        if(!empty($sql)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $post_id
     * @return bool
     * 判断帖子是否被锁定
     */
    public function judge_post_lock($post_id){
        $sql=!$this->Post_model->get_post_information($post_id)['lock'];
        if(!empty($sql)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param $user_id
     * @param $group_id
     * @return bool
     * 判断用户是否加入星球
     */
    public function check_group($user_id,$group_id){
        return $this->Group_model->check_group($user_id,$group_id);
    }

    /**
     * @param $group_id
     * @param $user_id
     * @return bool
     * 判断用户是否是星球创建者
     */
    public function judge_group_creator($group_id,$user_id){
        $re=$this->Group_model->get_group_infomation($group_id)['user_base_id'];
        if($re == $user_id){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $user_id
     * @param $post_id
     * @return bool
     * 判断用户是否是发帖者
     */
    public function judge_post_creator($user_id,$post_id){
        $re=$this->Post_model->get_post_information($post_id)['user_base_id'];
        if($re == $user_id){
            return true;
        }else{
            return false;
        }
    }
    public function judge_post_reply_user($user_id,$group_id,$floor){

    }

    /**
     * @param $user_id
     * @return bool
     * 判断用户是否是管理员 user_detail
     */
    public function judge_admin($user_id){
        $re=$this->User_model->get_user_information($user_id)['authorization'];
        if(in_array($re,array(02,03))){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $rs
     * @return mixed
     * 删除帖子内容中的html标签
     */
    public function delete_html_reply($rs){
        for ($i=0; $i<count($rs['reply']); $i++) {
            $rs['reply'][$i]['p_text'] = strip_tags($rs['reply'][$i]['p_text']);
        }
        return $rs;
    }

    /**
     * @param $p_id
     * @param $floor
     * @return bool|int
     * 查询帖子回复所在的页数
     */
    public function get_post_reply_page($p_id,$floor){
        return $this->Post_model->get_post_reply_page($p_id,$floor);
    }


    /*
     * 通过星球id获取星球名称
     */
    public function get_group_name($group_id){
        $sql=$this->db->select('name')
            ->where('id',$group_id)
            ->get('group_base')
            ->row_array();
        return $sql['name'];
    }

    /*
    通过星球id判断星球是否为私密星球
     */
    /*
     * @param $group_id
     * @return mixed
     * 已存在相同函数
    public function judge_group_private($group_id){
        $sql=$this->db->select('private')
            ->where('id',$group_id)
            ->get('group_base')
            ->row_array();
        return $sql['private'];
    }

    /*
    判断用户是否为星球成员
     */
    public function judge_group_user($group_id,$user_id){
        $sql=$this->db->select('*')
            ->where('group_base_id',$group_id)
            ->where('user_base_id',$user_id)
            ->where('authorization',03)
            ->get('group_detail')
            ->row_array();
        if(empty($sql)){
            $re=NULL;
        }else{
            $re=1;
        }
        return $re;
    }

    /*
    判断用户是否为星球创建者
    */
    /*
     * @param $group_id
     * @param $user_id
     * @return int|null
     * 已存在相同函数
    public function judge_group_creator($group_id,$user_id){
        $sql=$this->db->select('*')
            ->where('group_base_id',$group_id)
            ->where('user_base_id',$user_id)
            ->where('authorization',01)
            ->get('group_detail')
            ->row_array();
        if(empty($sql)){
            $re=NULL;
        }else{
            $re=1;
        }
        return $re;
    }

    /*
    判断用户是否在申请加入私有星球
    */
    public function judge_user_application($user_id,$group_id){
        $sql=$this->db->select('*')
            ->where_in('status',array(0,1))
            ->where('message_base_code','0001')
            ->where('id_1',$user_id)
            ->where('id_2',$group_id)
            ->get('message_detail')
            ->row_array();
        if(empty($sql)){
            $re=NULL;
        }else{
            $re=1;
        }
        return $re;
    }





}