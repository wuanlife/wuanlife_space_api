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
 * 判断用户是否存在
 */
    public function judgeExist($user_id){
        $model=new Model_User();
        $rs=$model->judgeExist($user_id);
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
}



