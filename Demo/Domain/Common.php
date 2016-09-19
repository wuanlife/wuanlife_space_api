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

    /*
    判断用户是否为星球创建者
     */
    public function judgeGroupCreator($group_id,$user_id){
        $model=new Model_Group();
        $re=$model->judgeGroupCreator($group_id,$user_id);
        return $re;
    }

    /*
    判断用户是否为星球成员
     */
    public function judgeGroupUser($group_id,$user_id){
        $model=new Model_Group();
        $re=$model->judgeGroupUser($group_id,$user_id);
        return $re;
    }

    /*
    通过星球id返回星球创建者姓名
     */
    public function getCreator($group_id){
        $model=new Model_Group();
        $re=$model->getCreator($group_id);

        return $re;
    }

    /*
    判断星球是否为私密星球
     */
    public function judgeGroupPrivate($group_id){
        $model=new Model_Group();
        $re =$model->judgeGroupPrivate($group_id);
        return $re;
    }

    /*
    获取星球名称
     */
    public function getGroupName($group_id){
        $model=new Model_Group();
        $re=$model->getGroupName($group_id);
        return $re;
     }

    /*
    判断用户是否为发帖者
     */
    public function judgePostUser($user_id,$post_id){
        $model=new Model_Post();
        $re=$model->judgePostUser($user_id,$post_id);
        return $re;
    }

    /*
    判断用户是否在线
     */
    public function judgeUserOnline($user_id){
        $data1 = array ('user_id' => $user_id);
        $data1 = http_build_query($data1);
        $opts = array (
        'http' => array (
            'method' => 'POST',
            'header'=> "Content-type: application/x-www-form-urlencoded" .
            "Content-Length:" . strlen($data1) . "rn",
            'content' => $data1
        )
        );

        $context = stream_context_create($opts);
        $html = file_get_contents('http://dev.wuanlife.com/news', false, $context);
        return $html;
    }

    /*
    通过星球id获取星球创建者id
     */
    public function getGroupCreate($group_id){
        $model=new Model_Group();
        $re=$model->getGroupCreate($group_id);
        return $re;
    }

    /*
    通过星球id获取星球创建者id
     */
    public function judgePrivate($private){
        if($private===1){
            $private=1;
        }else{
            $private=0;
        }
        return $private;
    }

    /*
    判断用户是否在申请加入私有星球
     */
    public function judgeUserApplication($user_id,$group_id){
        $model=new Model_Group();
        $re=$model->judgeUserApplication($user_id,$group_id);
        return $re;
    }
}