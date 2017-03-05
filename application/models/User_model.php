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
        $nickname=$data['nickname'];
        $email=$data['email'];
        $password=$data['password'];
        $regtime=time();
        $sql='insert into user_base (password,nickname,email,regtime) '.
            "values (\"$password\",\"$nickname\",\"$email\",\"$regtime\")";
        $query=$this->db->query($sql);

        if($query){
            $sql1="select id as userID,nickname,email from user_base where nickname=\"$nickname\"";
            $query=$this->db->query($sql1);
            $re=$query->result_array()[0];
        }else{
            $re=false;
        }
        return $re;
    }






}