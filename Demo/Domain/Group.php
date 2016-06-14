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

    public function save_base64_image($base64_image_string, $output_file_without_extentnion, $path_with_end_slash ) {
        $splited = explode(',', substr( $base64_image_string , 5 ) , 2);
        $mime=$splited[0];
        $data=$splited[1];
        $mime_split_without_base64=explode(';', $mime,2);
        $mime_split=explode('/', $mime_split_without_base64[0],2);
        if(count($mime_split)==2) {
            $extension=$mime_split[1];
            if($extension=='jpeg')$extension='jpg';
            $output_file_with_extentnion=$output_file_without_extentnion.'.'.$extension;
        }
        file_put_contents( $path_with_end_slash . $output_file_with_extentnion, base64_decode($data) );
        return $path_with_end_slash . $output_file_with_extentnion;
    }

    public function create($data) {
            $this->model = new Model_Group();
            $this->checkStatus($data['user_id']);
            $this->checkN($data['name']);
            //上传路径
            //base64编码测试
            //$data["g_image"]="data:image/jpg;base64,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";
            $date=date("Y/m/d");
            $RootDIR = dirname(__FILE__);
            $path=$RootDIR."/../../Public/demo/upload/group/$date/";
            $base64_image_string = $data["g_image"];
            $output_file_without_extentnion = time();
            $path_with_end_slash = "$path";
            if ($this->u_status == '1' && $this->g_status =='1') {
                if(!empty($data["g_image"])) {
                    //创建上传路径
                    if(!is_readable($path)) {
                        is_file($path) or mkdir($path,0777,true);
                    }
                    //调用接口保存base64字符串为图片
                    $filepath = $this->save_base64_image($base64_image_string, $output_file_without_extentnion, $path_with_end_slash );
                    $size = getimagesize ($filepath);
                    if($size[0]>94&&$size[1]>94){
                        include "../../Library/resizeimage.php";
                        $imageresize = new ResizeImage($filepath, 94, 94,1, $filepath);//裁剪图片
                    }
                        $data["g_image"] = substr($filepath,-39);
                }
                else{
                    $data["g_image"]=NULL;
                }
                    if(empty($data['g_introduction'])) {
                        $data['g_introduction']=NULL;
                    }
                $data = array('name' => $data['name'],'g_image'=>$data["g_image"],'g_introduction' => $data['g_introduction']) ;


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
				if(!empty($data["g_image"])) {$data["g_image"] = $_SERVER['HTTP_HOST'].substr($filepath,-39);}
                $this->rs['info']['URL'] = $data["g_image"];
                $this->rs['code'] = 1;
            }
            else{
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
        //base64编码测试
        //$data["p_image"]="data:image/jpg;base64,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";
        
        if ($this->u_status == '1' && $this->g_status == '1') {
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
            if(!empty($data["p_image"])) {
				$date=date("Y/m/d");
				$RootDIR = dirname(__FILE__);
				$path=$RootDIR."/../../Public/demo/upload/posts/$date/";
				$base64_image_string = $data["p_image"];
				$output_file_without_extentnion = time();
				$path_with_end_slash = "$path";
                //创建上传路径
                if(!is_readable($path)) {
                    is_file($path) or mkdir($path,0777,true);
                }
                //调用接口保存base64字符串为图片
                $filepath = $this->save_base64_image($base64_image_string, $output_file_without_extentnion, $path_with_end_slash );
                $size = getimagesize ($filepath);
                if($size[0]>94&&$size[1]>94){
                    include "../../Library/resizeimage.php";
                    $imageresize = new ResizeImage($filepath, 94, 94,1, $filepath);//裁剪图片
                }
                    $data["p_image"] = substr($filepath,-39);
            $i_data = array(
                'post_base_id'        => $pb['id'],
                'p_image'   => $data["p_image"],
            );
            $pi = DI()->notorm->post_image->insert($i_data);
					$pi['p_image']=$_SERVER['HTTP_HOST'].$pi['p_image'];
                    //$pi['p_image'] = DI()->notorm->post_image->select('p_image')->where('post_base_id =?', $pb['id'])->AND('post_image.delete=?','0')->fetchall();
            }
            else{
				$pi = NULL;
			}			
            $this->rs['code'] = 1;
            $this->rs['info'] = $pd;
            $this->rs['info']['title']=$pb['title'];
            $this->rs['info']['URL']=$pi['p_image'];
			$this->rs['info']['post_image_id']=$pi['id'];
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
        $this->pages['num']=$all_num;
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
        $this->pages['num']=$all_num;        
        return $this->model->getCreate($limit_st, $page_num,$user_id);
    }

}




 ?>