<?php

class Domain_Group {
    public $rs = array(
            'code' => 0,
            'msg'  => '',
            'info' => array(),
            );
    public $msg   = '';
    public $model = '';
    public $cookie = array();
    public $u_status = '0';
    public $g_status = '0';
    public $pages = array();

    public function checkN($g_name){
        $rs = $this->model->checkName($g_name);
        if (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u', $g_name)) {
            $this->msg = '小组名只能为中文、英文、数字或者下划线，但不得超过20字节！';
        }elseif (!empty($rs)) {
            $this->msg = '该星球已创建！';
        }else{
            $this->g_status = '1';
        }
    }

    public function checkG($g_id){
        $rs = $this->model->checkGroup($this->cookie['userID'], $g_id);
        if (!empty($rs)) {
            $this->msg = '已加入该星球！';
            $this->g_status = '1';
            // return $this->msg;
        }else{
            $this->g_status = '0';
            $this->msg = '未加入该星球！';
        }
    }

    public function checkStatus($user_id){
        // $config = array('crypt' => new Domain_Crypt(), 'key' => 'a secrect');
        // DI()->cookie = new PhalApi_Cookie_Multi($config);
        // $this->cookie['userID']   = DI()->cookie->get('userID');
        // $this->cookie['nickname'] = DI()->cookie->get('nickname');

        $rs = $this->model->getUser($user_id);
        $this->cookie['userID'] = $user_id;
        $this->cookie['nickname'] = $rs['nickname'];

        if (empty($this->cookie['nickname'])) {
            $this->msg = '用户尚未登录！';
            $this->u_status = '1';//为1取消用户登录验证，为0需要验证用户是否登录
            // return $this->msg;
        }else{
            $this->u_status = '1';
            $this->msg = '用户已登录！';
        }

        $rs = $this->model->getUser($user_id);
        $this->cookie['userID'] = $user_id;
        $this->cookie['nickname'] = $rs['nickname'];
    }

    public function create($data){
        $this->model = new Model_Group();
        $this->checkStatus($data['user_id']);
        $this->checkN($data['name']);
        //上传路径
        $date=date("Y/m/d");
		$RootDIR = dirname(__FILE__);
        $path=$RootDIR."/../upload/group/$date";
        if ($this->u_status == '1' && $this->g_status =='1') {
        //上传操作
        if(!is_readable($path)) {
            is_file($path) or mkdir($path,0777,true);
        }
        move_uploaded_file($_FILES["g_image"]["tmp_name"],
        "$path/" . date("His") . $_FILES["g_image"]["name"]);//移动文件
        if(empty($_FILES["g_image"]["name"])){
            $size=array(94,94);
        }
            else{
                $size = getimagesize ("$path/" . date("His") . $_FILES["g_image"]["name"]);
            }
        if($size[0]<=94&&$size[1]<=94){

        }
            else{
                include "../Library/resizeImage.php";
                $imageresize = new ResizeImage("$path/" .  date("His") . $_FILES["g_image"]["name"], 94, 94,1, "$path/" .  date("His") . $_FILES["g_image"]["name"]);//裁剪图片
            }
        if(empty($_FILES["g_image"]["name"])) {
             $_FILES["g_image"]["data"]=NULL;
        }
        else {
            $_FILES["g_image"]["data"] = "$path/" .  date("His") . $_FILES["g_image"]["name"];
        }
        if(empty($data['g_introduction'])) {
            $data['g_introduction']=NULL;
        }
        $data = array('name' => $data['name'],'g_image'=>$_FILES["g_image"]["data"],'g_introduction' => $data['g_introduction']) ;


            $result = DI()->notorm->group_base->insert($data);
            // $result = $this->model->add(group_base,$data);
            $data2 = array(
                'group_base_id' => $result['id'],
                'user_base_id'  => $this->cookie['userID'],
                'authorization'=>"01",
            );
            $result2 = DI()->notorm->group_detail->insert($data2);
            // $result2 = $this->model->add(group_detail,$data2);
            $this->rs['info'] = $result2;
            $this->rs['info']['name'] = $result['name'];
            $this->rs['info']['g_introduction'] = $result['g_introduction'];
            $this->rs['info']['URL'] = $result['g_image'];
            $this->rs['code'] = 1;
        }else{
            $this->rs['msg'] = $this->msg;
        }
        return $this->rs;
    }

    public function join($data){
        $this->model = new Model_Group();
        $this->checkStatus($data['user_id']);
        $this->checkG($data['g_id']);

        if ($this->u_status == '1' && $this->g_status == '0') {
            $data = array(
                'group_base_id' => $data['g_id'],
                'user_base_id'  => $this->cookie['userID'],
                'authorization' => "03",
            );

            $result = DI()->notorm->group_detail->insert($data);
            $this->rs['info'] = $result;
            $this->rs['code'] = 1;
        }else{
            $this->rs['msg'] = $this->msg;
        }

        return $this->rs;
    }

    public function uStatus($data){
        $this->model = new Model_Group();
        $this->checkStatus($data['user_id']);

        if ($this->u_status == '1') {
            $this->rs['info'] = $this->cookie;
            $this->rs['code'] = 1;
        }else{
            $this->rs['msg'] = $this->msg;
        }

        return $this->rs;
    }

    public function gStatus($data){
        $this->model = new Model_Group();
        $this->checkStatus($data['user_id']);
        $this->checkG($data['g_id']);

        if ($this->g_status == '1') {
            $this->rs['code'] = 1;
            $this->rs['msg']  = $this->msg;
        }else{
            $this->rs['code'] = 0;
            $this->rs['msg']  = $this->msg;
        }

        return $this->rs;
    }

    public function lists($page,$pages){
        $this->model  = new Model_Group();
        $all_num      = $this->model->getAllNum();              //总条
        $page_num     =empty($pages)?20:$pages;                 //每页条数
        $page_all_num =ceil($all_num/$page_num);                //总页数
        if ($page_all_num == 0){
            $page_all_num =1;
        }
        $page         =empty($page)?1:$page;                    //当前页数
        $page         =(int)$page;                              //安全强制转换
        $limit_st     =($page-1)*$page_num;                     //起始数

        $this->pages['pageCount'] = $page_all_num;
        $this->pages['currentPage'] = $page;
        return $this->model->lists($limit_st, $page_num);
    }

    public function posts($data){
        $this->model = new Model_Group();
        $this->checkStatus($data['user_id']);
        $this->checkG($data['group_base_id']);
        //上传路径
        $date=date("Y/m/d");
		$RootDIR = dirname(__FILE__);
		$path=$RootDIR."/../upload/posts/$date";
        if ($this->u_status == '1' && $this->g_status == '1') {
            //上传操作
            if(!is_readable($path)) {
                is_file($path) or mkdir($path,0777,true);
            }
            move_uploaded_file($_FILES["p_image"]["tmp_name"],
            "$path/" . date("His") . $_FILES["p_image"]["name"]);//移动文件
            if(empty($_FILES["p_image"]["name"])){
                $size=array(94,94);
            }
                else{
                    $size = getimagesize ("$path/" . date("His") . $_FILES["p_image"]["name"]);
                }
            if($size[0]<=94&&$size[1]<=94){

            }
            else{
                include "../Library/resizeImage.php";
                $imageresize = new ResizeImage("$path/" .  date("His") . $_FILES["p_image"]["name"], 94, 94,1, "$path/" .  date("His") . $_FILES["p_image"]["name"]);//裁剪图片
            }
            if(empty($_FILES["p_image"]["name"])) {
                $_FILES["p_image"]["data"]=NULL;
            }
            else {
                $_FILES["p_image"]["data"] = "$path/" .  date("His") . $_FILES["p_image"]["name"];
            }
            $b_data = array(
                'user_base_id'  => $this->cookie['userID'],
                'group_base_id' => $data['group_base_id'],
                'title'         => $data['title'],
            );
            $pb = DI()->notorm->post_base->insert($b_data);
            $time = date('Y-m-d H:i:s',time());
            $d_data = array(
                'post_base_id' => $pb['id'],
                'user_base_id' => $this->cookie['userID'],
                'text' => $data['text'],
                'floor'=> '1',
                'createTime' => $time,
            );
            $pd = DI()->notorm->post_detail->insert($d_data);
            $i_data = array(
                'id'        => $pb['id'],
                'p_image'   => $_FILES["p_image"]["data"],
            );
            $pi = DI()->notorm->post_image->insert($i_data);
            $this->rs['code'] = 1;
            $this->rs['info'] = $pd;
            $this->rs['info']['title']=$pb['title'];
            $this->rs['info']['URL']=$pi['p_image'];
        }else{
            $this->rs['msg'] = $this->msg;
        }

        return $this->rs;
    }

    public function getJoined($page,$pages,$user_id){
        $this->model  = new Model_Group();
        $all_num      = $this->model->getAllGroupJoinednum($user_id);              //总条
        $page_num     =empty($pages)?20:$pages;                 //每页条数
        $page_all_num =ceil($all_num/$page_num);                //总页数
        if ($page_all_num == 0){
            $page_all_num =1;
        }
        $page         =empty($page)?1:$page;                    //当前页数
        $page         =(int)$page;                              //安全强制转换
        $limit_st     =($page-1)*$page_num;                     //起始数

        $this->pages['pageCount'] = $page_all_num;
        $this->pages['currentPage'] = $page;
        return $this->model->getJoined($limit_st, $page_num,$user_id);
    }

    public function getCreate($page,$pages,$user_id){
        $this->model  = new Model_Group();
        $all_num      = $this->model->getAllGroupCreatenum($user_id);          //总条
        $page_num     =empty($pages)?2:$pages;                 //每页条数
        $page_all_num =ceil($all_num/$page_num);                //总页数
        if ($page_all_num == 0){
            $page_all_num =1;
        }
        $page         =empty($page)?1:$page;                    //当前页数
        $page         =(int)$page;                              //安全强制转换
        $limit_st     =($page-1)*$page_num;                     //起始数

        $this->pages['pageCount'] = $page_all_num;
        $this->pages['currentPage'] = $page;
        return $this->model->getCreate($limit_st, $page_num,$user_id);
    }

}




 ?>