<?php



class Group_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function create($data){

    }

    public function getUser($user_id){
        $sql="select nickname from user_base where id=$user_id";
        $query=$this->db->query($sql);
        $re=$query->result_array()[0];
        return $re['nickname'];
    }
    public function join($data){
        $this->db->insert('group_detail', $data);
    }
    public function check_group($user_id,$group_id){
        return $this->db->where('group_base_id',$group_id)
            ->where('user_base_id',$user_id)
            ->where('authorization','03')
            ->from('group_detail')
            ->get()
            ->row_array();
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
        foreach ($re as $key=>$value){
            $row[]=$value["group_base_id"];
        }
        $arr_string = join(',', $row);
        return $arr_string;
    }
}