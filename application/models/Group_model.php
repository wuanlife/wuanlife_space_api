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
        return $this->db->where('group_base_id',$group_id)
            ->where('user_base_id',$user_id)
            ->where('authorization','03')
            ->from('group_detail')
            ->get()
            ->row_array();
    }

}