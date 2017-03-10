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
        $query=$this->db->select('id')
            ->where('name',$data['g_name'])
            ->get('group_base')
            ->row_array();
        if($re){
            $detail=array(
                'group_base_id'=>$query['id'],
                'user_base_id'=>$data['user_id'],
                'authorization'=>'01',
            );
            $this->db->flush_cache();
            $re=$this->db->insert('group_detail',$detail);
        }
        return $re;
    }
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



    /*
     * 通过用户id获取用户nickname
     */
    public function get_user($user_id){
        $sql="select nickname from user_base where id=$user_id";
        $query=$this->db->query($sql);
        $re=$query->result_array()[0];
        return $re['nickname'];
    }


    /*
     * 通过用户id和星球id
     * 判断用户是否为星球创建者
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



    /*
     * 通过星球id判断星球是否存在
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

    public function get_group_info($group_id){
        $re=$this->db->select('id as groupID,name as groupName,g_introduction,g_image')
            ->from('group_base')
            ->where('id',$group_id)
            ->get()
            ->row_array();
        return $re;
    }

    public function alter_group_info($data){
        $re=$this->db->set('g_introduction',$data['g_introduction'])
            ->set('g_image',$data['g_image'])
            ->where('id',$data['group_id'])
            ->update('group_base');
        return $re;
    }









    public function join($data){
        $re=$this->db->insert('group_detail', $data);
        return $re;
    }
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
    public function get_all_group_num(){
        $this->db->from('group_base');
        return $this->db->count_all_results();
    }
    public function get_all_cgroup_num($user_id){
        $this->db->where('user_base_id', $user_id)->where('authorization','01')->from('group_detail');
        return $this->db->count_all_results();
    }
    public function get_all_jgroup_num($user_id){
        $this->db->where('user_base_id', $user_id)->where('authorization','03')->from('group_detail');
        return $this->db->count_all_results();
    }
    public function lists($limit_st,$page_num){
        $sql='SELECT gb.name AS g_name,gb.id AS group_id,gb.g_image,gb.g_introduction,COUNT(gd.user_base_id) AS num FROM group_detail gd, group_base gb '
            .'WHERE gb.id = gd.group_base_id AND gb.delete=0 '
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            ."LIMIT $limit_st,$page_num";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function get_create($limit_st,$page_num,$user_id){
        $query = $this->db->query("SELECT `group_base_id` FROM `group_detail` WHERE  `user_base_id` = $user_id AND `authorization` = '01'");
        $re = $query->result_array();
        $row = array();
        foreach ($re as $key=>$value){
            $row[]=$value["group_base_id"];
        }
        $arr_string = join(',', $row);
        $sql="SELECT gb.name,gb.id,gb.g_image,gb.g_introduction,COUNT(gd.group_base_id) AS num FROM group_base gb,group_detail gd "
            ."WHERE gb.delete=0 AND gb.id IN($arr_string) AND gb.id=gd.group_base_id "
            .'GROUP BY gb.id HAVING COUNT(gb.id)>=1 '
            .'ORDER BY COUNT(gd.group_base_id) DESC '
            ."LIMIT $limit_st,$page_num";
        $query = $this->db->query($sql);
        $re = $query->result_array();
        return $re;
    }
    public function get_joined($limit_st,$page_num,$user_id){
        $query = $this->db->query("SELECT `group_base_id` FROM `group_detail` WHERE  `user_base_id` = $user_id AND `authorization` = '03'");
        $re = $query->result_array();
        $row = array();
        foreach ($re as $key=>$value){
            $row[]=$value["group_base_id"];
        }
        $arr_string = join(',', $row);
        $sql="SELECT gb.name,gb.id,gb.g_image,gb.g_introduction,COUNT(gd.group_base_id) AS num FROM group_base gb,group_detail gd "
            ."WHERE gb.delete=0 AND gb.id IN($arr_string) AND gb.id=gd.group_base_id "
            .'GROUP BY gb.id HAVING COUNT(gb.id)>=1 '
            .'ORDER BY COUNT(gd.group_base_id) DESC '
            ."LIMIT $limit_st,$page_num";
        $query = $this->db->query($sql);
        $re = $query->result_array();
        return $re;
    }
    public function private_group($data,$user_id){
        $field = array(
            'user_base_id'      =>$user_id,
            'user_apply_id'     =>$data['user_id'],
            'group_base_id'     =>$data['group_id'],
            'create_time'        =>time(),
            'text'              =>$data['p_text'],
            'status'            =>0
        );
        return $this->db->insert('message_apply',$field);
    }
    public function user_manage($data){
        return $this->db->where('group_base_id',$data['group_id'])
            ->where('authorization','03')
            ->from('group_detail')
            ->get()
            ->result_array();
    }
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
    public function search_group($text,$gnum,$gn){
        if(empty($gn)){
            $re = array();
            return $re;
        }
        $text = strtolower($text);
        $num=($gn-1)*$gnum;
        $sql='SELECT gb.name,gb.id,gb.g_image,gb.g_introduction,COUNT(gd.user_base_id) AS num '
            .'FROM group_detail gd, group_base gb '
            .'WHERE gb.id = gd.group_base_id AND gb.delete=0 '
            ."AND lower(gb.name) LIKE '%$text%' "
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            ."LIMIT $num,$gnum";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function search_group_num($text){
        $text = strtolower($text);
        $sql='SELECT COUNT(group_base.id) AS num '
            .'FROM group_base '
            .'WHERE group_base.delete=0 '
            ."AND lower(group_base.name) LIKE '%$text%' ";
        $query = $this->db->query($sql);
        return $query->row_array()['num'];
    }
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