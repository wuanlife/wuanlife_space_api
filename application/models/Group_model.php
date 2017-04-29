<?php



class Group_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function create($data){
        $group=array(
            'name'=>$data['g_name'],
            'g_introduction'=>$data['g_introduction'],
            'g_image'=>$data['g_image'],
            'private'=>$data['private']
        );
        $re=$this->db->insert('group_base',$group);
        $this->db->flush_cache();
        $query=$this->db->select('name as g_name,id as group_id,g_image,g_introduction')
            ->where('name',$data['g_name'])
            ->get('group_base')
            ->row_array();
        if($re){
            $detail=array(
                'group_base_id'=>$query['group_id'],
                'user_base_id'=>$data['user_id'],
                'authorization'=>'01',
            );
            $this->db->flush_cache();
            $re=$this->db->insert('group_detail',$detail);
            $query['user_id']=$data['user_id'];
            $query['authorization']='01';
        }
        return $query;
    }
    /**
     * @param $group_id
     * @return mixed
     * 通过星球id获取星球详情
     */
    public function get_group_infomation($group_id){
        $this->db->select('*');
        $this->db->from('group_base');
        $this->db->where('id',$group_id);
        $this->db->join('group_detail', 'group_detail.group_base_id = group_base.id');
        $this->db->where('authorization',01);
        $query = $this->db->get();
        return $query->row_array();
    }

    /*
     * 根据星球名判断星球是否存在
     */
    public function gname_exist($g_name){
        $re=$this->db->select('id')
            ->where('name',$g_name)
            ->get('group_base')
            ->row_array();
        return $re['id'];
    }

    public function get_group_post_num($group_id)
    {
        $re=$this->db->select('id')
            ->where('group_base_id',$group_id)
            ->get('post_base')
            ->result_array();
        return count($re);
    }

    public function get_group_user_num($group_id)
    {
        $re=$this->db->select('user_base_id')
            ->where('group_base_id',$group_id)
            ->get('group_detail')
            ->result_array();
        return count($re);
    }

    /*
     * 通过用户id获取用户nickname
     */
    public function get_user($user_id){
        $sql="select nickname from user_base where id=$user_id";
        $query=$this->db->query($sql);
        $re=$query->result_array()[0];
        return $re['nickname'];
    }


    /**
     * @param $data
     * @return int
     * 通过用户id和星球id
     * 判断用户是否为星球创建者
     * 在Common已存在相同方法
     */
    public function judge_group_creator($data){
        $query=$this->db->select('user_base_id')
            ->from('group_detail')
            ->where('user_base_id',$data['user_id'])
            ->where('group_base_id',$data['group_id'])
            ->where('authorization','01')
            ->get()
            ->row_array();
        if($query){
            $re=1;
        }else{
            $re=0;
        }
        return $re;
    }


    public function quit_message($data){
        $creator = $this->get_group_infomation($data['group_id'])['user_base_id'];
        $field = array(
            'user_base_id'      =>$creator,
            'user_notice_id'     =>$data['user_id'],
            'group_base_id'     =>$data['group_id'],
            'create_time'        =>time(),
            'type'              =>4,
            'status'            =>0
        );
        $this->db->insert('message_notice',$field);
    }
    public function quit($data){
        $this->db->where('group_base_id',$data['group_id'])
            ->where('user_base_id',$data['user_id'])
            ->where('authorization','03')
        ->delete('group_detail');
    }

    public function g_status($data){
        $query=$this->db->select('user_base_id')
            ->from('group_detail')
            ->where('user_base_id',$data['user_id'])
            ->where('group_base_id',$data['g_id'])
            ->get()
            ->row_array();
        if($query){
            $re=1;
        }else{
            $re=0;
        }
        return $re;
    }



    /**
     * @param $group_id
     * @return int
     * 通过星球id判断星球是否存在
     * 在Common已存在相同方法
     */
    public function judge_group_exist($group_id){
        $query=$this->db->select('id')
            ->from('group_base')
            ->where('id',$group_id)
            ->get()
            ->row_array();
        if($query){
            $re=1;
        }else{
            $re=0;
        }
        return $re;
    }


    public function alter_group_info($data){
        $re=$this->db->set('g_introduction',$data['g_introduction'])
            ->set('g_image',$data['g_image'])
            ->set('private',$data['private'])
            ->where('id',$data['group_id'])
            ->update('group_base');
        return $re;
    }

    public function join_message($data){
        $creator = $this->get_group_infomation($data['group_base_id'])['user_base_id'];
        $field = array(
            'user_base_id'      =>$creator,
            'user_notice_id'     =>$data['user_base_id'],
            'group_base_id'     =>$data['group_base_id'],
            'create_time'        =>time(),
            'type'              =>5,
            'status'            =>0
        );
        $this->db->insert('message_notice',$field);
    }







    public function join($data){
        $re=$this->db->insert('group_detail', $data);
        return $re;
    }
    /**
     * @param $user_id
     * @param $group_id
     * @return bool
     * 判断用户是否已加入星球
     */
    public function check_group($user_id,$group_id){
        $re =  $this->db->where('group_base_id',$group_id)
            ->where('user_base_id',$user_id)
            ->where('authorization','03')
            ->from('group_detail')
            ->get()
            ->row_array();
        if(!empty($re)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @return int
     * 获取所有星球数量
     */
    public function get_all_group_num(){
        $this->db->from('group_base');
        return $this->db->count_all_results();
    }
    /**
     * @param $user_id
     * @return int
     * 获取用户星球数量
     */
    public function get_user_group_num($user_id){
        $this->db->from('group_detail')
            ->where('user_base_id', $user_id)
            ->join('group_base','group_detail.group_base_id = group_base.id')
            ->where('delete',0);
        return $this->db->count_all_results();
    }

    /**
     * @param $limit_st
     * @param $page_num int 每页数量
     * @return mixed
     * 获取所有星球列表
     */
    public function lists($limit_st,$page_num){
        $sql='SELECT gb.name AS g_name,gb.id AS group_id,gb.g_image,gb.g_introduction,COUNT(gd.user_base_id) AS num FROM group_detail gd, group_base gb '
            .'WHERE gb.id = gd.group_base_id AND gb.delete=0 '
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            ."LIMIT $limit_st,$page_num";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @param $limit_st
     * @param $page_num
     * @param $user_id
     * @return mixed
     * 获取用户加入和创建星球
     */
    public function get_user_group($limit_st,$page_num,$user_id){
        $query = $this->db->query("SELECT `group_base_id` FROM `group_detail` WHERE  `user_base_id` = $user_id");
        $re = $query->result_array();
        $row = array();
        foreach ($re as $key=>$value){
            $row[]=$value["group_base_id"];
        }
        $arr_string = join(',', $row);
        if(empty($arr_string))
        {
            return false;
        }else
        {
            $sql="SELECT gb.name AS g_name,gb.id AS group_id,gb.g_image,gb.g_introduction,COUNT(gd.group_base_id) AS num FROM group_base gb,group_detail gd "
                ."WHERE gb.delete=0 AND gb.id IN($arr_string) AND gb.id=gd.group_base_id "
                .'GROUP BY gb.id HAVING COUNT(gb.id)>=1 '
                .'ORDER BY COUNT(gd.group_base_id) DESC '
                ."LIMIT $limit_st,$page_num";
            $query = $this->db->query($sql);
            $re = $query->result_array();
            return $re;
        }

    }

    /**
     * @param $data
     * @param $user_id
     * @return bool
     * 私密星球申请
     */
    public function private_group($data,$user_id){
        $field = array(
            'user_base_id'      =>$user_id,
            'user_apply_id'     =>$data['user_id'],
            'group_base_id'     =>$data['group_id'],
            'create_time'        =>time(),
            'text'              =>$data['text'],
            'status'            =>0
        );
        $boolean =  $this->db->insert('message_apply',$field);
        if($boolean){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @param $data
     * @return mixed
     * 星球成员管理
     */
    public function user_manage($data){
        return $this->db->where('group_base_id',$data['group_id'])
            ->where('authorization','03')
            ->from('group_detail')
            ->get()
            ->result_array();
    }
    /**
     * @param $data
     * @return bool
     * 删除星球成员
     */
    public function delete_group_member($data){
        $boolean = $this->check_group($data['member_id'],$data['group_id']);
        if($boolean == NULL){
            return false;
        }else{
            $this->db->where('group_base_id',$data['group_id'])
                ->where('user_base_id',$data['member_id'])
                ->where('authorization','03')
                ->delete('group_detail');
            return true;
        }
    }
    /**
     * @param $data
     * @return CI_DB_active_record
     * 删除星球成员消息反馈给成员
     */
    public function dgm_message($data){
        $field=array(
            'user_base_id'      =>$data['member_id'],
            'user_notice_id'    =>$data['user_id'],
            'group_base_id'     =>$data['group_id'],
            'create_time'       =>time(),
            'status'            =>0,
            'type'              =>3
        );
        return $this->db->insert('message_notice',$field);
    }
    /**
     * @param $text
     * @param $gnum
     * @param $gn
     * @return mixed
     * 搜索星球
     */
    public function search_group($text,$gnum,$gn){
        $text = strtolower($text);
        $num=($gn-1)*$gnum;
        $sql='SELECT gb.name AS g_name,gb.id AS group_id,gb.g_image,gb.g_introduction,COUNT(gd.user_base_id) AS num '
            .'FROM group_detail gd, group_base gb '
            .'WHERE gb.id = gd.group_base_id AND gb.delete=0 '
            ."AND lower(gb.name) LIKE '%$text%' "
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            ."LIMIT $num,$gnum";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @param $text
     * @return mixed
     * 搜索星球数量
     */
    public function search_group_num($text){
        $text = strtolower($text);
        $sql='SELECT COUNT(group_base.id) AS num '
            .'FROM group_base '
            .'WHERE group_base.delete=0 '
            ."AND lower(group_base.name) LIKE '%$text%' ";
        $query = $this->db->query($sql);
        return $query->row_array()['num'];
    }
    /**
     * @param $user_id
     * @return string
     * 测试接口，待全部完成之后删除
     */
    public function test($user_id){
        $query = $this->db->query("SELECT `group_base_id` FROM `group_detail` WHERE  `user_base_id` = $user_id AND `authorization` = '01'");
        $re = $query->result_array();
        $row = array();
        foreach ($re as $key=>$value){
            $row[]=$value["group_base_id"];
        }
        $arr_string = join(',', $row);
        return $arr_string;
    }
    /**
     * @param $data
     * @return mixed
     * 发表帖子
     */
    public function posts($data){
        $b_data = array(
            'user_base_id'  => $data['user_id'],
            'group_base_id' => $data['group_id'],
            'title'         => $data['p_title'],
        );
        $time = date('Y-m-d H:i:s',time());
        $this->db->insert('post_base',$b_data);
        $d_data = array(
            'post_base_id' => $this->db->insert_id('post_base'),
            'user_base_id' => $data['user_id'],
            'text' => $data['p_text'],
            'floor'=> '1',
            'create_time' => $time,
        );
        $this->db->insert('post_detail',$d_data);
        return $d_data['post_base_id'];
    }










}