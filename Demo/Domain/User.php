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

}



