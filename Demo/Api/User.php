<?php
/**
 * 登录注册服务类
 */

class Api_User extends PhalApi_Api{

    public function getRules(){
        return array(

            'login' => array(
                'Email'    => array(
                    'name'    => 'Email',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户邮箱'
                ),

                'password' => array(
                    'name'    => 'password',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户密码'
                ),
            ),
            'reg' => array(
                'nickname' => array(
                    'name'    => 'nickname',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户昵称'
                ),

                'Email'    => array(
                    'name'    => 'Email',
                    'type'    => 'string',
                    'require' => true,
                    'min'     => '1',
                    'desc'    => '用户邮箱'
                ),

                'password' => array(
                    'name'    => 'password',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户密码'
                ),
            ),
            'alterUserInfo'=>array(
                'user_id' => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id'
                ),
                'user_name' => array(
                    'name'    => 'user_name',
                    'type'    => 'string',
                    'require' => false,
                    'desc'    => '用户昵称'
                ),
                'profile_picture' => array(
                    'name'    => 'profile_picture',
                    'type'    => 'string',
                    'require' => false,
                    'default' => NULL,
                    'desc'    => '用户头像'
                ),
                'sex' => array(
                    'name'    => 'sex',
                    'type'    => 'int',
                    'require' => false,
                    'default' => 0,
                    'desc'    => '用户性别'
                ),
                'year' => array(
                    'name'    => 'year',
                    'type'    => 'string',
                    'require' => false,
                    'default' => NULL,
                    'desc'    => '年份'
                ),
                'month' => array(
                    'name'    => 'month',
                    'type'    => 'string',
                    'require' => false,
                    'default' => NULL,
                    'desc'    => '月份'
                ),
                'day' => array(
                    'name'    => 'day',
                    'type'    => 'string',
                    'require' => false,
                    'default' => NULL,
                    'desc'    => '天数'
                ),
            ),
            'getUserInfo'=>array(
                'user_id' => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id'
                ),
            ),
            'SendMail' => array(
                'Email'    => array(
                    'name'    => 'Email',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户邮箱'
                ),
            ),
            'CheckMail' => array(
                'Email'    => array(
                    'name'    => 'Email',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户邮箱'
                ),
            ),
            'mailChecked' => array(
                'Email'    => array(
                    'name'    => 'Email',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户邮箱'
                ),
                'code'    => array(
                    'name'    => 'code',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '验证码'
                ),
            ),
            'RePsw' => array(
                'Email'    => array(
                    'name'    => 'Email',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户邮箱'
                ),

                'code'    => array(
                    'name'    => 'code',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '验证码'
                ),

                'password'    => array(
                    'name'    => 'password',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户密码'
                ),

                'psw'    => array(
                    'name'    => 'psw',
                    'type'    => 'string',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户二次确认密码'
                ),
            ),
            'getMailChecked' => array(
                    'user_id' => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id'
                ),
            ),
            'ProcessApp'     => array(
                'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),
                /*
                'group_id' => array(
                    'name'    => 'group_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '星球ID'
                ),
                'applicant_id' =>array(
                    'name'    => 'applicant_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '申请人ID'
                ),
                */
                'message_id' =>array(
                    'name'    => 'message_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '消息ID'
                ),
                'mark'    => array(
                    'name'    => 'mark',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '标识符，1为同意，0为拒绝'
                ),
            ),
            'ShowMessage' =>array(
                'user_id'   => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),
                'pn'   => array(
                    'name'    => 'pn',
                    'type'    => 'int',
                    'default' => '1',
                    'require' => false,
                    'desc'    => '用户ID'
                ),
                'status'   => array(
                    'name'    => 'status',
                    'type'    => 'int',
                    'default' => '1',
                    'require' => false,
                    'desc'    => '是否已读'
                ),
                'mtype'   => array(
                    'name'    => 'mtype',
                    'type'    => 'int',
                    'default' => '1',
                    'require' => false,
                    'desc'    => '消息中心分类'
                ),
            ),
            /*和消息列表接口合并，不再单独给接口。2016/09/20
            'alterRead'=>array(
                'message_code'   => array(
                    'name'    => 'message_code',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '消息类型，0001、0002、0003分别代表申请，同意，拒绝'
                ),
                'user_id'   => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),
                'countnum'   => array(
                    'name'    => 'countnum',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '计数参数，用于区分同一类型的消息'
                ),
            ),
            */
            'CheckNewInfo'=>array(
                'user_id'   => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),
            ),
        );
    }

/**
 * 登录接口
 * @desc 用于验证并登录用户
 * @return int code 操作码，1表示登录成功，0表示登录失败
 * @return object info 用户信息对象
 * @return int info.id 用户ID
 * @return string info.nickname 用户昵称
 * @return string msg 提示信息
 *
 */
    public function login(){
        $rs = array('code' => '', 'msg' => '','info' => array());
        $data = array(
            'Email'    => $this->Email,
            'password' => $this->password,
            );
        $domain = new Domain_User();
        $rs = $domain->login($data);
        return $rs;

    }

/**
 * 注册接口
 * @desc 用于验证并注册用户
 * @return int code 操作码，1表示注册成功，0表示注册失败
 * @return object info 用户信息对象
 * @return int info.id 用户ID
 * @return string info.nickname 用户昵称
 * @return string msg 提示信息
 *
 */
    public function reg(){
        $rs = array('code' => '', 'msg' => '', 'info' => array());
        $data = array(
            'nickname' => $this->nickname,
            'Email'    => $this->Email,
            'password' => $this->password,
            'regtime'  => time(),
            );
        $domain = new Domain_User();
        $rs = $domain->reg($data);
        return $rs;

    }
/**
 * 注销接口
 * @desc 用于清除用户登录状态
 * @return int code 操作码，1表示注销成功，0表示注销失败
 * @return string msg 提示信息
 */
    public function logout() {
        $rs = array('code' => '', 'msg' => '');
        $domain = new Domain_User();
        $rs = $domain->logout();
        return $rs;
    }



/**
 * 邮件发送接口
 * @desc 用于发送包含重置密码验证码的邮件
 * @return int code 操作码，1表示发送成功，0表示发送失败
 * @return string msg 提示信息
 *
 */
    public function SendMail(){
        $data = array(
            'Email'    => $this->Email,
            'num'      => 1,
            );
        $domain = new Domain_User();
        $rs = $domain->SendMail($data);
        return $rs;

    }
/**
 * 邮箱验证接口
 * @desc 用于发送包含验证邮箱验证码的邮件
 * @return int code 操作码，1表示发送成功，0表示发送失败
 * @return string msg 提示信息
 *
 */
    public function CheckMail(){
        $data = array(
            'Email'    => $this->Email,
            'num'      => 2,
            );
        $domain = new Domain_User();
        $rs = $domain->SendMail($data);
        return $rs;
    }
/**
 * 邮箱验证接口
 * @desc 用于检验验证码的正确性并验证邮箱
 * @return int code 操作码，1表示验证成功，0表示验证失败
 * @return string msg 提示信息
 *
 */
    public function mailChecked(){
        $data = array(
            'Email'    => $this->Email,
            'code'     => $this->code,
            'num'      => 2,
            );
        $domain = new Domain_User();
        $rs = $domain->mailChecked($data);
        return $rs;
    }
/**
 * 找回密码接口
 * @desc 用于检验验证码的正确性并找回密码
 * @return int code 操作码，1表示验证成功，0表示验证失败
 * @return string msg 提示信息
 *
 */
    public function RePsw(){
        $data = array(
            'code'    => $this->code,
            'password'=> $this->password,
            'psw'     => $this->psw,
            'Email'   => $this->Email,
            'num'     => 1,
            );
        $domain = new Domain_User();
        $rs = $domain->RePsw($data);
        return $rs;
    }
/**
 *获取用户信息
 * @desc 用于获取用户的信息
 * @return int userID 用户id
 * @return string Email 用户Email
 * @return string nickname 用户名称
 * @return int sex 用户性别,0为未设，1为男，2为女
 * @return string year 年
 * @return string month 月
 * @return string day 日
 * @return string mailChecked 是否验证邮箱，0为未验证邮箱，1为已验证邮箱
 */
    public function getUserInfo(){
        $domain=new Domain_User();
        $rs=$domain->getUserInfo($this->user_id);
        return $rs;

    }

/**
 *修改用户信息接口
 * @desc 修改用户的信息
 * @return int data 1代表成功修改，0代表修改失败
 */
    public function alterUserInfo(){
        $domain=new Domain_User();
        $data = array(
                    'nickname'      =>$this->user_name,
                    'profile_picture'   =>$this->profile_picture,
                    'sex'           =>$this->sex,
                    'year'          =>$this->year,
                    'month'         =>$this->month,
                    'day'           =>$this->day,
        );
        $rs=$domain->alterUserInfo($this->user_id,$data);
        return $rs;
    }

/**
 *确认邮箱验证接口
 * @desc 用于验证用户邮箱是否已被验证
 * @return int userID 用户id
 * @return string mailChecked 是否验证邮箱，0为未验证邮箱，1为已验证邮箱
 */
    public function getMailChecked(){
        $domain=new Domain_User();
        $rs=$domain->getMailChecked($this->user_id);
        return $rs;

    }
/**
 * 处理申请者加入私密星球的申请接口
 * @desc 用于同意或者拒绝申请人加入私密星球
 * @return int code 操作码，1表示操作成功，0表示操作失败,2表示调用函数失败
 * @return string msg 提示信息
 */
    public function ProcessApp(){
        $data = array(
            'user_id'       => $this->user_id,
            /*
            'group_id'      => $this->group_id,
            'applicant_id'  => $this->applicant_id,
            'count'         => $this->count,
            */
            'message_id'      => $this->message_id,
            'mark'          => $this->mark,
            );
        $domain = new Domain_User();
        $rs = $domain->ProcessApp($data);
        $u_id = $domain->getMessageInfo($data['message_id']);
        $common=new Domain_Common();
        $re=$common->judgeUserOnline($u_id['id_1']);
        if(empty($re)){
            $rs->code=2;
        }
        return $rs;
    }
/**
 * 用户消息中心接口
 * @desc 用于接收其他用户发送给用户消息
 * @return int code 操作码，1表示接收成功，0表示没有新消息
 * @return array info 用户消息列表详情
 * @return string info.information 用户消息详情
 * @return string info.createTime 创建时间
 * @return string info.status 消息状态 0未读 1已读 2已同意 3已拒绝
 * @return string info.messagetype 消息类型
 * @return string msg 提示信息
 */
    public function ShowMessage(){
        $data = array(
            'user_id'       => $this->user_id,
            'pn'            => $this->pn,
            'status'        => $this->status,
            'messageType'   => $this->mtype,
            );
        $domain = new Domain_User();
        $rs = $domain->ShowMessage($data);
        return $rs;
    }
/**
 * 已读接口
 * @desc 用于将未读消息标记为已读
 * @return int code 操作码，1表示操作成功，0表示操作失败
 * @return string msg 提示信息
 */
    /*和消息列表接口合并，不再单独给接口。2016/09/20
    public function alterRead(){
        $data = array(
            'message_code'  => $this->message_code,
            'user_id'       => $this->user_id,
            'countnum'         => $this->countnum,
            );
        $domain = new Domain_User();
        $rs = $domain->alterRead($data);
        return $rs;
    }
    */
/**
 * 未读信息条数接口
 * @desc 用于返回未读消息条数
 * @return int num 1代表有新信息，0代表没有
 */
    public function CheckNewInfo(){
        $data = array(
            'user_id'       => $this->user_id,
            );
        $domain = new Domain_User();
        $rs = $domain->CheckNewInfo($data);
        return $rs;
    }

}