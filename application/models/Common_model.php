<?php



class Common_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }


    /*
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
    判断用户是否在线(调用前端接口)
     */
    public function judgeUserOnline($user_id){
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
    }
    /*
     * 判断星球是否有头像，若没有给默认头像
     */
    public function judge_image_exist($lists){
        for($i=0;$i<count($lists);$i++){
            if(empty($lists[$i]["g_image"])||$lists[$i]["g_image"]==null){
                $lists[$i]["g_image"]='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
        }
        return $lists;
    }

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