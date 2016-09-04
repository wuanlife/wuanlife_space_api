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
        $rs=$model->judgeGroupExist($group_id);
        return $rs;
    }

    /*
    判断星球是否有头像，若没有给默认头像
     */
    public function judgeImageExist($lists){
        for($i=0;$i<count($lists);$i++){
            if(empty($lists[$i]["g_image"])||$lists[$i]["g_image"]==null){
                $lists[$i]["g_image"]='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
        }
        return $lists;
    }
}