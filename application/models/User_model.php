<?php



class User_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }


    /*
     * 判断邮箱是否已注册
     */
    public function user_email($data){
       $email=$data['email'];
       $re=false;

        $query = $this->db->query("select * from user_base where email=\"$email\"");
        if($query->result_array()){
           $re=$query->result_array()[0];
        }
        return $re;
    }

    /*
     * 判断用户昵称是否已使用
     */
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
        $password=md5($password);
        $regtime=time();
        $sql='insert into user_base (password,nickname,email,regtime) '.
            "values (\"$password\",\"$nickname\",\"$email\",\"$regtime\")";
        $query=$this->db->query($sql);
        if($query){
            $sql2="select id as user_id,nickname as user_name,email as user_email from user_base where nickname=\"$nickname\"";
            $query2=$this->db->query($sql2);
            $re=$query2->result_array()[0];
            $user_id=$re['user_id'];
            $sql1='insert into user_detail (user_base_id,authorization) '.
                "values (\"$user_id\",'01')";
            $this->db->query($sql1);
        }else{
            $re=false;
        }
        return $re;
    }

    public function get_user_info($user_id){
        $sql='select user_base_id as user_id,sex,year,month,day,mail_checked,profile_picture from user_detail '.
            "where user_base_id=$user_id";
        $sqlb="select email as user_email,nickname from user_base where id=$user_id";
        $query=$this->db->query($sql)->result_array()[0];
        $queryb=$this->db->query($sqlb)->result_array()[0];
        if(empty($query['profile_picture'])){
            //给无头像用户加上默认头像
            $query['profile_picture']='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
        }
        $query['user_email']=$queryb['user_email'];
        $query['user_name']=$queryb['nickname'];
        return $query;

    }


    public function alter_user_info($data){
        extract($data);
        $sql="update user_detail set profile_picture=\"$profile_picture\",sex=\"$sex\",year=\"$year\",month=\"$month\",day=\"$day\" ".
            "where user_base_id=$user_id";
        $query=$this->db->query($sql);
        return $query;
    }

    public function get_user_information($user_id){
        $this->db->select('*');
        $this->db->from('user_base');
        $this->db->where('id',$user_id);
        $this->db->join('user_detail', 'user_detail.user_base_id = user_base.id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_group_menber($group_id){
        $this->db->select('*');
        $this->db->from('group_detail');
        $this->db->where('group_base_id',$group_id);
        $this->db->where('authorization','03');
        $query = $this->db->get();
        return $query->result_array();
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
    public function get_num_message($user_id,$table,$num=NULL){
        $this->db->from($table);
        $this->db->where('user_base_id',$user_id);
        if(!empty($num)){
            $this->db->where('status',0);
        }
        if($table=='message_reply'){
            $this->db->where('status',0);
            $this->db->or_where('status',1);
        }
        return $this->db->count_all_results();
    }
    public function get_num_reply_message($user_id){
        $this->db->from('message_reply');
        $this->db->where('user_base_id',$user_id);
        return $this->db->count_all_results();
    }
    public function get_num_apply_message($user_id){
        $this->db->from('message_apply');
        $this->db->where('user_base_id',$user_id);
        return $this->db->count_all_results();
        /*
        //$this->db->from('message_apply');
        $array = array('user_base_id'=>$user_id);
        //$this->db->where($array);
        if($status!=1){
            if($status==2){
                $str="status='2' OR status='3' OR status='4'";
            }elseif($status==3) {
               $str = "status='0' OR status='1'";
            }
            //$this->db->where($str);
        }
        //$apply_num = $this->db->count_all_results();
        $type = "type='1' OR type='2'";
        $notice_num = $this->get_num_notice_message($user_id,$str,$type);
        return $notice_num;
        */
    }
    public function get_num_notice_message($user_id){
        $this->db->from('message_notice');
        $this->db->where('user_base_id',$user_id);
        return $this->db->count_all_results();
        /*
        $this->db->from('message_notice');
        $array = array('user_base_id'=>$user_id);
        $this->db->where($array);
        if(!empty($status)){
            $this->db->where($status);
        }
        $this->db->where($type);
        return $this->db->count_all_results();
        */
    }

    public function alter_status($value,$status,$table){
        $data['status'] = $status;
        if(empty($value['id'])){
            $re = $this->db->update($table, $data, array('user_base_id'=>$value['user_base_id'],'status'=>$value['status']));
        }else{
            $re = $this->db->update($table, $data, array('id'=>$value['id'],'status'=>1));
        }
        return $re;
    }
    public function show_reply_message($data,$page_num){
        $user_id = $data['user_id'];
        $limsit_st = ($data['pn']-1)*$page_num;
        $sql='SELECT ub.id AS user_id,mr.id AS m_id,mr.reply_floor,ud.profile_picture,ub.nickname AS user_name,pb.title AS p_title,pb.id AS post_id '
            .'FROM user_base ub,post_base pb,message_reply mr,user_detail ud '
            ."WHERE mr.user_base_id = \"$user_id\" AND mr.user_reply_id = ub.id AND mr.post_base_id = pb.id AND ud.user_base_id = ub.id "
            .'AND mr.status = 1 '
            .'ORDER BY mr.create_time DESC '
            ."LIMIT $limsit_st,$page_num";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function show_apply_message($data,$page_num){
        $user_id = $data['user_id'];
        $limsit_st = ($data['pn']-1)*$page_num;
        $sql='SELECT ub.id AS user_id,ma.id AS m_id,ma.text,ud.profile_picture,ub.nickname AS user_name,gb.name AS g_name,gb.id AS group_id,ma.status '
            .'FROM user_base ub,group_base gb,message_apply ma,user_detail ud '
            ."WHERE ma.user_base_id = \"$user_id\" AND ma.user_apply_id = ub.id AND ma.group_base_id = gb.id AND ud.user_base_id = ub.id "
            .'ORDER BY ma.create_time DESC '
            ."LIMIT $limsit_st,$page_num";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function show_notice_message($data,$page_num){
        $user_id = $data['user_id'];
        $limsit_st = ($data['pn']-1)*$page_num;
        $sql='SELECT ub.id AS user_id,mn.id AS m_id,mn.type,ub.nickname AS user_name,gb.name AS g_name,gb.id AS group_id,mn.status '
            .'FROM user_base ub,group_base gb,message_notice mn '
            ."WHERE mn.user_base_id = \"$user_id\" AND mn.user_notice_id = ub.id AND mn.group_base_id = gb.id "
            .'ORDER BY mn.create_time DESC '
            ."LIMIT $limsit_st,$page_num";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function get_message_info($m_id){
        $this->db->select('*')->from('message_apply')->where('id',$m_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function process_app_info($data){
        $this->db->insert('message_notice',$data);
    }
    public function check_new_info($id){
        $table = array('message_notice','message_apply','message_reply');
        $num = array();
        for($i=0;$i<3;$i++){
            $num[$i] = $this->get_num_message($id,$table[$i],1);
        }
        return $num;
    }





}