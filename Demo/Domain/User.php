<?php

class Domain_User {

    /*
    登录检查

    */

    public function login($data){
        $model = new Model_User();
        $rs = $model->login($data);
        return $rs;
    }
    /*
    注册检查

    */

    public function reg($data){
        $model = new Model_User();
        $rs = $model->reg($data);
        return $rs;
    }
    /*
	注销检查

    */
    public function logout(){
        $model = new Model_User();
        $rs = $model->logout();
        return $rs;
    }


/*
 * 判断用户是否为管理员
 */
    public function judgeAdmin($user_id){
        $model=new Model_User();
        $rs=$model->judgeAdmin($user_id);
        return $rs;
    }

/*
 * 判断用户是否为星球创建者
 */
    public function judgeCreate($user_id,$group_id){
        $model=new Model_User();
        $rs=$model->judgeCreate($user_id,$group_id);
        return $rs;
    }

    public function getUserInfo($user_id){
        $model=new Model_User();
        $rs=$model->getUserInfo($user_id);
        return $rs;
    }

    public function alterUserInfo($user_id,$sex,$year,$month,$day){
        $model=new Model_User();
        $rs=$model->alterUserInfo($user_id,$sex,$year,$month,$day);
        return $rs;
    }
/*
 * 发送邮件
 */

	public function SendMail($data){
        $model = new Model_User();
        $rs = $model->SendMail($data);
        return $rs;
    }	
/*
 * 验证邮箱
 */
	public function CheckMail($data){
        $model = new Model_User();
        $rs = $model->CheckMail($data);
        return $rs;
    }	
/*
 * 校验验证码并修改密码
 */

	public function RePsw($data){
        $model = new Model_User();
        $rs = $model->RePsw($data);
        return $rs;
    }

/*
 * 验证用户邮箱是否已被验证
 */

    public function getMailChecked($user_id){
        $model = new Model_User();
        $rs = $model->getMailChecked($user_id);
        return $rs;
    }
}

