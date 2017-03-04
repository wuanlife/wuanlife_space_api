<?php



class User_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }



    public function user_email($data){
       $email=$data['email'];
       $re=false;

        $query = $this->db->query("select * from user_base where email=\"$email\"");
        if($query->result_array()){
           $re=$query->result_array()[0];
        }
        return $re;
    }

    public function user_nickname($data){
        $nickname=$data['nickname'];
        $re=false;
        $query=$this->db->query("select nickname from user_base where nickname=\"$nickname\"");
        if($query->result_array()){
            $re=$query->result_array();
        }
        return $re;

    }

    public function reg($data){
/*        $nickname=$data['nickname'];
        $email=$data['email'];
        $password=$data['password'];
        $regtime=time();
        $query=$this->db->query("insert into user_base values ()");*/
    }






}