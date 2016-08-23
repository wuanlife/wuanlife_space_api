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
				'sex' => array(
                    'name'    => 'sex',
                    'type'    => 'int',
                    'require' => false,
                    'desc'    => '用户性别'
                ),
				'year' => array(
                    'name'    => 'year',
                    'type'    => 'string',
                    'require' => false,
                    'desc'    => '年份'
                ),
				'month' => array(
                    'name'    => 'month',
                    'type'    => 'string',
                    'require' => false,
                    'desc'    => '月份'
                ),
				'day' => array(
                    'name'    => 'day',
                    'type'    => 'string',
                    'require' => false,
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
 * @desc 用于发送包含修改密码验证码的邮件
 * @return int code 操作码，1表示发送成功，0表示发送失败
 * @return string msg 提示信息
 *
 */
    public function SendMail(){
        $data = array(
            'Email'    => $this->Email,
			'num'      => 0,
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
			'num'      => 1,
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
 *修改星球接口
 * @desc 修改星球的信息
 * @return int data 1代表成功修改，0代表修改失败
 */
    public function alterUserInfo(){
        $domain=new Domain_User();
        $rs=$domain->alterUserInfo($this->user_id,$this->sex,$this->year,$this->month,$this->day);
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
}