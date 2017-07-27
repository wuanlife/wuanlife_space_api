<?php



class User_model extends CI_Model
{
    /**
     * 构造函数，提前执行
     * User_model constructor.
     */
    public function __construct()
    {
        $this->load->database();
    }


    /**
     * 判断邮箱是否已注册
     * @param $data
     * @return bool
     */
    public function user_email($data){
        $this->db->select('*');
        $this->db->from('user_base');
        $this->db->where('email',$data['email']);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * 判断用户昵称是否已使用
     * @param $nickname
     * @return bool
     */
    public function user_nickname($nickname){
        $this->db->select('nickname');
        $this->db->from('user_base');
        $this->db->where('nickname',$nickname);
        $query = $this->db->get();
        return $query->row_array();

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
     * 获取用户邀请码
     * @param $user_id
     * @return mixed
     */
    public function show_code($user_id){
        $this->db->select('code,used as num')->from('user_code')->where('user_base_id',$user_id)->where('difference',3);
        $query = $this->db->get()->row_array();
        return $query;
    }

    /**
     * 将生成的邀请码存入数据库
     * @param $user_id
     * @param $i_code
     *
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

    /**
     * 用户注册
     * @param $data
     * @return bool|int
     */
    public function reg($data){
        $data['regtime'] = time();
        if($this->db->insert('user_base', $data)){
            $user_id = $this->db->insert_id('user_base');
            if($this->db->insert('user_detail',[
                'user_base_id'=>$user_id,
                'authorization'=>'01',
                'last_logtime' =>$data['regtime'],
                'mail_checked'  =>0,
                'profile_picture'   =>'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
            ]))
            {
                return $user_id;
            }
        }
        return FALSE;
    }

    /**
     * 修改用户信息
     * @param $data
     * @return mixed
     */
    public function alter_user_info($data){
        $nickname = $data['nickname'];
        unset($data['nickname']);
        $this->db->where('user_base_id', $data['user_base_id']);
        $this->db->update('user_detail', $data)?$msg['m']=1:$msg['m']=2;
        if(!empty($nickname)){
            $username=$this->user_nickname($nickname);
            if(empty($username)){
                $this->db->set('nickname',$nickname)
                    ->where('id',$data['user_base_id'])
                    ->update('user_base')?$msg['n']=1:$msg['n']=2;
            }else{
                $msg['n'] = 3;
            }
        }
        return $msg;
    }

    /**
     * 通过用户id获取用户消息
     * @param $user_id
     * @return mixed
     *
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
     * 通过星球id获取星球成员信息，无用途，后续会删除     *2017/7/25 0025
     * @param $group_id
     * @return mixed
     *
     */
    public function get_group_menber($group_id){
        $this->db->select('*');
        $this->db->from('group_detail');
        $this->db->where('group_base_id',$group_id);
        $this->db->where('authorization','03');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 用户登录，无用途，后续会删除     *2017/7/25 0025
     * @param $data
     * @return bool
     */
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
     * 获取用户消息的数量
     * @param $user_id
     * @param $table string 数据表名称
     * @param null $status
     * @return int
     *
     */
    public function get_num_message($user_id,$table,$status=NULL)
    {
        $this->db->from($table);
        $this->db->where('user_base_id',$user_id);
        if($status===TRUE){
            $this->db->where('status',0);
        }
        if($status===FALSE){
            $this->db->where_in('status',[0,1]);
        }
        return $this->db->count_all_results();

    }

    /**
     * 修改消息状态
     * @param $value
     * @param $status
     * @param $table string 数据表名称
     * @return bool
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
     * 获取帖子通知，以后要改成用查询构造器查询或者参数绑定
     * @param $user_id      int     用户ID
     * @param $limit        int     每页显示数
     * @param $offset       int     起始数
     * @return mixed
     */
    public function show_reply_message($user_id,$limit,$offset){
        $sql='SELECT ub.id AS user_id,mr.id AS m_id,mr.reply_floor,ud.profile_picture,ub.nickname AS user_name,pb.title AS p_title,pb.id AS post_id '
            .'FROM user_base ub,post_base pb,message_reply mr,user_detail ud '
            ."WHERE mr.user_base_id = {$user_id} AND mr.user_reply_id = ub.id AND mr.post_base_id = pb.id AND ud.user_base_id = ub.id "
            .'AND mr.status = 1 '       //帖子可能被删除，相应的通知也会被删除，所以这里不是数据库所有数据返回
            .'ORDER BY mr.create_time DESC '
            ."LIMIT $offset,$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 获取私密星球申请通知，以后要改成用查询构造器查询或者参数绑定
     * @param $user_id
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function show_apply_message($user_id,$limit,$offset){
        $sql='SELECT ub.id AS user_id,ma.id AS m_id,ma.text,ud.profile_picture,ub.nickname AS user_name,gb.name AS g_name,gb.id AS group_id,ma.status '
            .'FROM user_base ub,group_base gb,message_apply ma,user_detail ud '
            ."WHERE ma.user_base_id = {$user_id} AND ma.user_apply_id = ub.id AND ma.group_base_id = gb.id AND ud.user_base_id = ub.id "
            .'ORDER BY ma.create_time DESC '
            ."LIMIT $offset,$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 获取星球通知，以后要改成用查询构造器查询或者参数绑定
     * @param $user_id      int     用户ID
     * @param $limit        int     每页显示数
     * @param $offset       int     起始数
     * @return mixed
     */
    public function show_notice_message($user_id,$limit,$offset){
        $sql='SELECT ub.id AS user_id,mn.id AS m_id,mn.type,ub.nickname AS user_name,gb.name AS g_name,gb.id AS group_id,mn.status '
            .'FROM user_base ub,group_base gb,message_notice mn '
            ."WHERE mn.user_base_id = {$user_id} AND mn.user_notice_id = ub.id AND mn.group_base_id = gb.id "
            .'ORDER BY mn.create_time DESC '
            ."LIMIT $offset,$limit";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 通过消息id获取私密星球申请详情
     * @param $m_id int 用户id
     * @return mixed
     *
     */
    public function get_message_info($m_id){
        $this->db->select('*')->from('message_apply')->where('id',$m_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * 将私密星球申请存表，通知创建者
     * @param $data
     *
     */
    public function process_app_info($data){
        $this->db->insert('message_notice',$data);
    }

    /**
     * 检测是否有新消息
     * @param $id
     * @return array
     *
     */
    public function check_new_info($id){
        $table = array('message_notice','message_apply','message_reply');
        $num = array();
        for($i=0;$i<3;$i++){
            $num[$i] = $this->get_num_message($id,$table[$i],TRUE);
        }
        return $num;
    }

    /**
     * 重置密码
     * @param $data
     * @return bool
     */
    public function re_psw($data){
        $this->db->where('id', $data['user_id']);
        return $this->db->update('user_base', [
            'password'=>$data['password']
        ]);
    }

    /**
     * 查看邮箱是否被验证
     * @param $user_id
     * @return bool
     *
     */
    public function get_mail_checked($user_id){
        $query = $this->db->select('mail_checked')
            ->where('user_base_id', $user_id)
            ->from('user_detail')
            ->get()
            ->row_array();
        return $query['mail_checked']?TRUE:FALSE;

    }

    /**
     * 将用户邮箱状态更新为已验证
     * @param $id
     * @return bool
     */
    public function check_mail($id)
    {
        $this->db->where('user_base_id', $id);
        return $this->db->update('user_detail', ['mail_checked' => 1]);
    }

    /**
     * 判断用户是否是星球创建者，无用途，后续会删除       *2017/7/25 0025
     * @param $user_id
     * @param $group_id
     * @return int
     *
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
     * 判断用户是否是管理员 user_detail
     * @param $user_id
     * @return int
     *
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
