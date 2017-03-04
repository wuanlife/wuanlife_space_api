<?php



class User_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }



    public function login($data){
       $email=$data['email'];
       $re=false;

        $query = $this->db->query("SELECT * FROM user_base where email=\"$email\"");
        if($query->result_array()){
           $re=$query->result_array()[0];
        }
        return $re;
    }




}