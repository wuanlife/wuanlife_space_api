<?php


/**
 * @property User_model      $User_model
 * @property Group_model    $Group_model
 * @property Post_model     $Post_model
 *
 */
class Common_model extends CI_Model
{
    /**
     * 构造函数，提前执行
     * Common_model constructor.
     */
    public function __construct()
    {
        $this->load->database();
        $this->load->model('User_model');
        $this->load->model('Group_model');
        $this->load->model('Post_model');
    }


    /**
     * 判断是否为私密 多余方法，后续会移除 *2017/7/25 0025
     */
    /**
 *    public function judgePrivate($private){
        if($private==1){
            $private=1;
        }else{
            $private=0;
        }

        return $private;
    }*/

    /**
     *`判断用户是否在线(调用前端接口)
     */
    /**
      * public function judgeUserOnline($user_id){
         $data1 = array ('userid' => $user_id);
         $data1 = http_build_query($data1);
         $RootDIR = dirname(__FILE__);
         $path=$RootDIR."/../../Public/init.php";
         require_once $path;
         $url=DI()->config->get('sys.url');
         $opts = array (
             'http' => array (
                 'method' => 'POST',
                 'header'=> "Content-type: application/x-www-form-urlencoded",
                 'content' => $data1
             )
         );
         $context = stream_context_create($opts);
         $html = file_get_contents($url, false, $context);
         return $html;
    }*/

    /**
     * 判断星球是否有头像，若没有给默认头像
     * @param $lists
     * @return
     */
    public function judge_image_exist($lists){
        for($i=0;$i<count($lists);$i++){
            if(empty($lists[$i]["image_url"])||$lists[$i]["image_url"]==null){
                $lists[$i]["image_url"]='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
        }
        return $lists;
    }

    /**
     * 判断星球的私密性
     * @param $group_id
     * @return int
     *
     */
    public function judge_group_private($group_id){
        $re =$this->Group_model->get_group_infomation($group_id)['private'];
        return $re;
    }


    /**
     * 判断星球是否存在
     * @param $group_id
     * @return int
     */
    public function judge_group_exist($group_id){
        $delete=$this->Group_model->get_group_infomation($group_id)['delete'];
        if($delete==='0')
        {
            return true;
        }
        return false;
    }

    /**
     * 判断帖子是否存在
     * @param $post_id
     * @return int
     *
     */
    public function judge_post_exist($post_id){
        $group_id = $this->Post_model->get_post_information1($post_id)['group_base_id'];
        $rs=$this->judge_group_exist($group_id);
        if($rs){
            $sql=!$this->Post_model->get_post_information1($post_id)['delete'];
            return $sql;
        }
        return false;
    }

    /**
     * 判断帖子是否被锁定 多余方法 后续移除 *2017/7/25 0025
     * @param $post_id
     * @return bool
     *
     */
    public function judge_post_lock($post_id){
        $sql=!$this->Post_model->get_post_information1($post_id)['lock'];
        if(!empty($sql)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 判断用户是否加入星球，存在相同方法，后续会移除*2017/7/25 0025
     * @param $user_id
     * @param $group_id
     * @return bool
     *
     */
/**
 *    public function check_group($user_id,$group_id){
        return $this->Group_model->check_group($user_id,$group_id);
    }*/

    /**
     * 判断用户是否是星球创建者
     * @param $group_id
     * @param $user_id
     * @return bool
     *
     */
    public function judge_group_creator($group_id,$user_id){
        $re=$this->Group_model->get_group_infomation($group_id)['user_base_id'];
        if($re == $user_id&&!empty($user_id)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 判断用户是否是发帖者
     * @param $user_id
     * @param $post_id
     * @return bool
     *
     */
    public function judge_post_creator($user_id,$post_id){
        $re=$this->Post_model->get_post_information1($post_id)['user_base_id'];
        if($re == $user_id){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 判断用户是否是管理员 user_detail
     * @param $user_id
     * @return bool
     *
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
     * 删除帖子内容中的html标签，无用方法，后续会移除*2017/7/25 0025
     * @param $rs
     * @return mixed
     *
     */
    public function delete_html_reply($rs){
        for ($i=0; $i<count($rs['reply']); $i++) {
            $rs['reply'][$i]['p_text'] = strip_tags($rs['reply'][$i]['p_text']);
        }
        return $rs;
    }

    /**
     * 查询帖子回复所在的页数
     * @param $p_id
     * @param $floor
     * @return bool|int
     *
     */
    public function get_post_reply_page($p_id,$floor){
        return $this->Post_model->get_post_reply_page($p_id,$floor);
    }

    /**
     * 判断用户是否为星球成员
     * @param $group_id
     * @param $user_id
     * @return bool
     */
    public function judge_group_user($group_id,$user_id){
        $sql=$this->db->select('*')
            ->where('group_base_id',$group_id)
            ->where('user_base_id',$user_id)
            ->where('authorization',03)
            ->get('group_detail')
            ->row_array();
        if(empty($sql)){
            return FALSE;
        }else{
            return TRUE;
        }
    }


    /**
     * 判断用户是否在申请加入私有星球
     * @param $user_id
     * @param $group_id
     * @return int|null
     *
    */
    public function judge_user_application($user_id,$group_id){
        $sql=$this->db->select('*')
            ->where_in('status',array(0,1))
            ->where('user_apply_id',$user_id)
            ->where('group_base_id',$group_id)
            ->get('message_apply')
            ->row_array();
        if(empty($sql)){
            $re=NULL;
        }else{
            $re=1;
        }
        return $re;
    }

    /**
     * 判断是否存在收藏帖子
     * @param $data
     * @return bool
     */
    public function ifexist_collect_post($data){
        $sql=$this->db->select('post_base_id')
            ->from('user_collection')
            ->where('post_base_id',$data['post_id'])
            ->where('user_base_id',$data['user_id'])
            ->get()
            ->row_array();
        if($sql){
            return TRUE;
        }else{
            return FALSE;
        }
    }





}
