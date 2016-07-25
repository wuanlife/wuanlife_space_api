<?php

/**
 * Created by PhpStorm.
 * User: asus1
 * Date: 2016/7/20
 * Time: 11:25
 */
class Domain_Common
{
    /*
     * 判断用户是否存在
     */
    public function judgeUserExist($user_id){
        $model=new Model_User();
        $rs=$model->judgeUserExist($user_id);
        return $rs;

    }

    /*
 * 判断帖子是否存在
 * */
    public function judgePostExist($post_id){
        $model=new Model_Post();
        $rs=$model->judgePostExist($post_id);
        return $rs;
    }

    /*
 * 判断星球是否存在
 * */
    public function judgeGroupExist($group_id){
        $model=new Model_Group();
        $rs=$model->judgePostExist($group_id);
        return $rs;
    }    
}