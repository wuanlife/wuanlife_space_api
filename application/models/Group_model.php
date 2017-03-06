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

}