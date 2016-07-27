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
            'judgeCreate'=>array(
                'user_id' => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id'
                ),
                'group_id' => array(
                    'name'    => 'group_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '星球id'
                ),
            ),

            'getUser'=>array(
                'user_id' => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id'
                ),
            ),

            'modifyUser'=>array(
                'user_id'=>array(
                    'name'=>'user_id',
                    'type'=>'int',
                    'require'=>true,
                    'desc'=>'用户id'
                    ),
                'sex'=>array(
                    'name'=>'sex',
                    'type'=>'string',
                    'require'=>true,
                    'desc'=>'性别'
                    ),
                'year'    => array(
                    'name'    => 'year',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '年',
                ),
                'month'    => array(
                    'name'    => 'month',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '月',
                ),
                'day'    => array(
                    'name'    => 'day',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '日',
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

    public function judgeCreate(){
        $domain = new Domain_User();
        $rs =$domain->judgeCreate($this->user_id,$this->group_id);
        return $rs;
    }

/**
 *获取用户信息
 * @return int  ret   操作码 200代表成功
 * @return object data 用户信息对象
 * @return string data.Email 用户Email
 * @return string data.nickname 用户名称
 * @return string data.sex 用户性别
 * @return string data.year年
 * @return string data.month月
 * @return string data.day日
 * @return string data.testmail 是否验证邮箱
 * @return string msg 报错信息
 */
    public function getUser(){
        $domain=new Domain_User();
        $rs=$domain->getUser($this->user_id);
        return $rs;
    }

/**
 *修改星球详情
 * @return int  ret   操作码 200代表成功
 * @return int data 1代表成功修改 0代表没有改动
 * @return string msg 信息
 */
    public function modifyUser(){
        $domain=new Domain_User();
        $rs=$domain->modifyUser($this->user_id,$this->sex,$this->year,$this->month,$this->day);
        return $rs;
    }

}