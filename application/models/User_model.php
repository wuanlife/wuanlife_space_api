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
        $username=$this->user_nickname($data);
        if(empty($username)){
            $query=$this->db->set('nickname',$nickname)
                ->where('id',$user_id)
                ->update('user_base');
            $re['code']=1;
            $re['msg']='修改成功!';
            return $re;
        }
        $re['code']=0;
        $re['msg']='用户名被占用，其他资料修改成功！';
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

        $query = $this->db->query("SELECT * FROM user_base where email=\"$email\"");
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
    /**
     * 获取用户消息，合并为一个方法
    public function get_num_reply_message($user_id){
        $this->db->from('message_reply');
        $this->db->where('user_base_id',$user_id);
        return $this->db->count_all_results();
    }
    public function get_num_apply_message($user_id){
        $this->db->from('message_apply');
        $this->db->where('user_base_id',$user_id);
        return $this->db->count_all_results();
    }
    public function get_num_notice_message($user_id){
        $this->db->from('message_notice');
        $this->db->where('user_base_id',$user_id);
        return $this->db->count_all_results();
        }
        */

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
     * [生成5位数字验证码]
     * @return [type] [description]
     */
    public function code() {
        $char_len = 5;
        //$font = 6;
        $char = array_merge(/*range('A','Z'),range('a','z'),*/range('1','9'));//生成随机码值数组，不需要0，避免与O冲突
        $rand_keys = array_rand($char,$char_len);//随机生成$char_len个码值的键；
        if($char_len == 1) {//判断码值长度为一时，将其放入数组中
            $rand_keys = array($rand_keys);
        }
        shuffle($rand_keys);//打乱数组
        $code = '';
        foreach($rand_keys as $key) {
            $code .= $char[$key];
        }//拼接字符串
    
        return $code;
    }
/**
 * 查询邮箱对应的信息
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
    public function userEmail($data){
        
        $user_email = $data['user_email'];
        
        $rs = $this->db->select('*')->from('user_base')->where("email = '$user_email'")->get()->row_array();;
        return $rs;
    }

    /*
 * 获取数据库保存的验证码
 */
    public function getcode($data) {
        $sql = $this->User_model->userEmail($data);
        $id  = $sql['id'];
        $num = $data['num'];
        $row = $this->db->select('*')->from('user_code')->where(array('user_base_id' =>$id,'difference'=>$num))->get()->row_array();
        //print_r($row);
        return $row;
    }

    /*
 * 重置密码
 */
    public function repsw($data){
        $sql = $this->User_model->userEmail($data);
        
        $password= $data['password'];
        $id = $sql['id'];
        $sqla = $this->db->query("update user_base set password = $password where id = $id");
    }

    /*
 * 更新数据库中的验证码
 */
    public function updatecode($i_code,$data){
        $sql = $this->User_model->userEmail($data);
        $id = $sql['id'];
            $sqla = $this->db->query("update user_code set used = 0, code = $i_code where user_base_id = $id");

    }
/*
 * 发送邮件验证码
 */
    public function sendmail($data)
    {
        $data['user_email'] = stripslashes(trim($data['user_email']));
        $data['user_email'] = $this->User_model->injectChk($data['user_email']);
        $rs = $this->User_model->userEmail($data);
        return $rs;

    }

    /*
 * 防止注入
 */
    public function injectChk($sql_str) {
        /*php5.3起不再支持eregi()函数
        相关链接http://www.t086.com/article/5086
        */
        //$check = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
        $check = preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i', $sql_str);
        if ($check) {
            echo('您的邮箱格式包含非法字符，请确认！');
            exit ();
        }else {
            return $sql_str;
        }
    }
    public function send($data) {
    
        $this->load->library('email');

        $email = $data['user_email'];
        $this->email->from('wuanlife@163.com','午安网团队');
        $this->email->to("$email");
        if($data['num'] == 1)
        {
            $this->email->subject('找回密码');
            $this->email->message('<a href="https://www.baidu.com/">找回密码</a>');
        }
        else if($data['num'] == 2)
        {
        $this->email->subject('午安验证码');
        $code = $this->User_model->code();
        $this->User_model->updatecode($code,$data);
        $this->email->message(date('Y-m-d H:i:s').'您的验证码为'.$code);
    }
    
    //$this->email->message(date('Y-m-d H:i:s').'<a href="https://www.baidu.com/">找回密码</a>');
    
    if($this->email->send());
    {
        $msg = "发送成功";
    }
    return $msg;
    }
    public function getmailchecked($user_id){
        $rs = $this->db->query("select mail_checked from user_detail where user_base_id = 85")->row();
        return $rs->mail_checked;

    }

    public function change_pwd(){
        
    }




}