<?php



class User_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }


    /**
     * @param $data
     * @return bool
     * 判断邮箱是否已注册
     */
    public function user_email($data){
       $email=$data['email'];
       $re=false;

        $query = $this->db->query("select * from user_base where email=\"$email\"");

        return $query->row_array();
    }

    /**
     * @param $data
     * @return bool
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
    /**
     * @param $code
     * @return mixed
     * 判断邀请码是否存在
     */
    public function invite_code($code){
        $this->db->select('code,used')->from('user_code')->where('code',$code)->where('difference',3);
        $query = $this->db->get()->row_array();
        return $query;
    }
    /**
     * @param $user_id
     * @return mixed
     * 查找用户邀请码
     */
    public function show_code($user_id){
        $this->db->select('code as i_code,used as num')->from('user_code')->where('user_base_id',$user_id)->where('difference',3);
        $query = $this->db->get()->row_array();
        return $query;
    }
    /**
     * @param $user_id
     * @param $i_code
     * 生成邀请码
     */
    public function create_code($user_id,$i_code){
        $data = array(
            'code' => $i_code,
            'difference' => 3,
            'used' => 99,
            'user_base_id'=>$user_id,
            'get_pass_time'=>time(),
        );
        $this->db->insert('user_code', $data);
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
        $this->db->query($sql);
        if(!empty($nickname)){
            $username=$this->user_nickname($data);
            if(empty($username)){
                $this->db->set('nickname',$nickname)
                    ->where('id',$user_id)
                    ->update('user_base');
                $re['code']=1;
                $re['msg']='修改成功!';
            }else{
                $re['code']=0;
                $re['msg']='用户名被占用！';
            }
            return $re;
        }
        $re['code']=1;
        $re['msg']='修改成功！';
        return $re;
    }

    /**
     * @param $user_id
     * @return mixed
     * 通过用户id获取用户消息
     */
    public function get_user_information($user_id){
        $this->db->select('*');
        $this->db->from('user_base');
        $this->db->where('id',$user_id);
        $this->db->join('user_detail', 'user_detail.user_base_id = user_base.id');
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * @param $group_id
     * @return mixed
     * 通过星球id获取星球成员信息
     */
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

        $query = $this->db->query("SELECT * FROM user_base where email='$email'");
        if($query->result_array()){
           $re=$query->result_array()[0];
        }
        return $re;
    }
    /**
     * @param $user_id
     * @param $table string 数据表名称
     * @param null $num
     * @return int
     * 获取消息的数量
     */
    public function get_num_message($user_id,$table,$num=NULL)
    {
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

    /**
     * @param $value
     * @param $status
     * @param $table string 数据表名称
     * @return CI_DB_active_record
     * 修改消息状态
     */
    public function alter_status($value,$status,$table){
        $data['status'] = $status;
        if(empty($value['id'])){
            $re = $this->db->update($table, $data, array('user_base_id'=>$value['user_base_id'],'status'=>$value['status']));
        }else{
            $re = $this->db->update($table, $data, array('id'=>$value['id'],'status'=>1));
        }
        return $re;
    }
    /**
     * @param $data
     * @param $page_num int 每页数量
     * @return mixed
     * 获取帖子通知
     */
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
    /**
     * @param $data
     * @param $page_num int 每页数量
     * @return mixed
     * 获取私密星球申请通知
     */
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
    /**
     * @param $data
     * @param $page_num
     * @return mixed
     * 获取星球通知
     */
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
    /**
     * @param $m_id int 用户id
     * @return mixed
     * 通过消息id获取私密星球申请详情
     */
    public function get_message_info($m_id){
        $this->db->select('*')->from('message_apply')->where('id',$m_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    /**
     * @param $data
     * 将私密星球申请存表，通知创建者
     */
    public function process_app_info($data){
        $this->db->insert('message_notice',$data);
    }
    /**
     * @param $id
     * @return array
     * 检测是否有新消息
     */
    public function check_new_info($id){
        $table = array('message_notice','message_apply','message_reply');
        $num = array();
        for($i=0;$i<3;$i++){
            $num[$i] = $this->get_num_message($id,$table[$i],1);
        }
        return $num;
    }

    /**
 * 重置密码
     * @param $data
 */
    public function re_psw($data){
        $password = $data['password'];
        $id = $data['user_id'];
        $this->db->query("update user_base set password = '$password' where id = $id");
    }

    /**
     * @param $user_id
     * @return mixed
     * 查看邮箱是否被验证
     */
    public function get_mail_checked($user_id){
        $query = $this->db->select('mail_checked')
            ->where('user_base_id', $user_id)
            ->from('user_detail')
            ->get()
            ->row_array();
        return $query['mail_checked'];

    }

    /**
     * @param $data
     * @return mixed
     * 将用户邮箱状态更新为已验证
     */
    public function check_mail($data)
    {
        $this->db->where('user_base_id', $data['user_id']);
        $this->db->update('user_detail', ['mail_checked' => 1]);
        return $this->get_mail_checked($data['user_id']);
    }

    /**
     * @param $user_id
     * @param $group_id
     * @return int
     * 判断用户是否是星球创建者
     */
    public function judge_create($user_id, $group_id){
        $sql=$this->db->select('user_base_id')
            ->from('group_detail')
            ->where('user_base_id',$user_id)
            ->where('group_base_id',$group_id)
            ->where('authorization','01')
            ->get('')
            ->row_array();
        if(!empty($sql)){
            $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }

    /**
     * @param $user_id
     * @return int
     * 判断用户是否是管理员 user_detail
     */
    public function judge_admin($user_id){
        $sql=$this->db->select('authorization')
            ->from('user_detail')
            ->where('user_base_id',$user_id)
            ->where('authorization','02')
            ->get()
            ->row_array();
        if(!empty($sql)){
                $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }





}
