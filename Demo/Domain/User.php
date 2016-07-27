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

    public function getUser($user_id){
        $model=new Model_User();
        $rs=$model->getUser($user_id);
        return $rs;
    }

    public function modifyUser($user_id,$sex,$year,$month,$day){
        $model=new Model_User();
        $rs=$model->modifyUser($user_id,$sex,$year,$month,$day);
        return $rs;
    }
}



