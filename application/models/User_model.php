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
        extract($data);
        $regtime=time();
        $sql='insert into user_base (password,nickname,email,regtime) '.
            "values (\"$password\",\"$nickname\",\"$email\",\"$regtime\")";
        $query=$this->db->query($sql);


        if($query){
            $sql2="select id as userID,nickname,email from user_base where nickname=\"$nickname\"";
            $query2=$this->db->query($sql2);
            $re=$query2->result_array()[0];
            $user_id=$re['userID'];
            $sql1='insert into user_detail (user_base_id,authorization) '.
                "values (\"$user_id\",'01')";
            $this->db->query($sql1);
        }else{
            $re=false;
        }
        return $re;
    }

    public function getUserInfo($user_id){
        $sql='select user_base_id as userID,sex,year,month,day,mail_checked,profile_picture from user_detail '.
            "where user_base_id=$user_id";
        $sqlb="select email,nickname from user_base where id=$user_id";
        $query=$this->db->query($sql)->result_array()[0];
        $queryb=$this->db->query($sqlb)->result_array()[0];
        if(empty($query['profile_picture'])){
            //给无头像用户加上默认头像
            $query['profile_picture']='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
        }
        $query['email']=$queryb['email'];
        $query['nickname']=$queryb['nickname'];
        return $query;

    }


    public function alterUserInfo($data){
        extract($data);
        $sql="update user_detail set profile_picture=\"$profile_picture\",sex=\"$sex\",year=\"$year\",month=\"$month\",day=\"$day\" ".
            "where user_base_id=$user_id";
        $query=$this->db->query($sql);
        return $query;
    }






}