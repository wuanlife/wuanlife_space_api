<?php



class Post extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        // $this->load->model('User_model');
        // $this->load->model('Group_model');
        $this->load->model('Common_model');
        $this->load->helper(array('form', 'url','url_helper'));
        $this->load->library(array('form_validation','jwt'));
        $this->form_validation->set_message('required', '{field} 参数是必填选项.');
        $this->form_validation->set_message('min_length', '{field} 参数长度不小于{param}.');
        $this->form_validation->set_message('max_length', '{field} 参数长度不大于{param}.');
        $this->form_validation->set_message('is_natural_no_zero', '{field}不是正整数.');
        $this->form_validation->set_message('is_natural', '{field}不是自然数.');
    }

    /**
     * 首页帖子接口
     */
    public function index_get(){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        if(empty($jwt)){
            $user_id = NULL;
        }else{
            $token = $this->parsing_token($jwt);
            $user_id = $token->user_id;
        }

        //输入参数验证
        $data = array(
            'user_id'   => $user_id?:0,
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
            'latest'    => $this->get('latest')=='false'?FALSE:TRUE
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('lists') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        $re['data'] = $this->Post_model->get_post($data);

        if(empty($re['data'])){
            $this->response('',204);
        }
        $re=$this->Post_model->get_image_url($re);              //解析帖子内容，获得帖子中包含的图片
        $re=$this->Post_model->delete_image_gif($re);           //删除帖子中gif格式的图片
        $re=$this->Post_model->post_image_limit($re);           //展示图片限制，目前是显示三张
        $re=$this->Post_model->delete_html_posts($re);          //从帖子内容去除 HTML 和 PHP 标记
        $re=$this->Post_model->post_text_limit($re);            //帖子内容长度显示，目前是300字符

        foreach($re['data'] as $key=>$value){
            $re['data'][$key] = [
                'post' => [
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'content' => $value['content'],
                    // 'lock'    => $value['lock'],
                    'create_time' => $value['create_time'],
                    'approved' => $value['approved']?TRUE:FALSE,
                    'approved_num' => $value['approved_num'],
                    'collected' => $value['collected']?TRUE:FALSE,
                    'collected_num'     => $value['collected_num'],
                    /*此处待修改*/     'replied'   => $value['replied']?TRUE:FALSE,
                    /*此处待修改*/     'replied_num'   => $value['replied_num'],
                    'image_url'      => $value['image'],
                ],
                'user' => [
                    'avatar_url' =>$value['profile_picture']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
                    'name'       =>$value['nickname'],
                    'id'         =>$value['user_base_id'],
                ],
                'group' => [
                    'id'  => $value['group_base_id'],
                    'name'    => $value['name'],
                ],
            ];
        }

        //分页
        $all_num = $this->Post_model->get_post($data,TRUE);
        $offset = $data['offset'];
        $limit = $data['limit'];
        $page_count  = (ceil($all_num/$limit)-1);                   //比总页数小 1
        $finallyo = $page_count * $limit;
        $lasto = ($offset-$limit)>0?($offset-$limit):0;
        $nexto = ($offset+$limit)<$finallyo?($offset+$limit):$finallyo;
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                ''
            );

        $re['paging'] = [
            'first'=> "{$host}/posts?limit={$limit}&offset=0",
            'previous'=>"{$host}/posts?limit={$limit}&offset={$lasto}",
            'next'=> "{$host}/posts?limit={$limit}&offset={$nexto}",
            'final'=> "{$host}/posts?limit={$limit}&offset={$finallyo}"
        ];

        $this->response($re);
    }

    /**
     * 点赞帖子
     */
    public function approve_post($post_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'post_id'=>$post_id,
            'floor'=>$this->get('floor')?:1
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_post_base') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        $rs=$this->Post_model->get_approve_post($data);
        if($rs){
            $this->Post_model->update_approve_post($data)?
                $this->response('',204):
                $this->response(['error'=>'操作失败'],400);
        }else{
            $this->Post_model->add_approve_post($data)?
                $this->response('',204):
                $this->response(['error'=>'操作失败'],400);
        }

    }

    /**
     * 每个星球页面帖子显示
     */
    public function group_post_get($group_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        if(empty($jwt)){
            $user_id = NULL;
        }else{
            $token = $this->parsing_token($jwt);
            $user_id = $token->user_id;
        }

        //输入参数校验
        $data = [
            'user_id' => $user_id?:0,
            'group_id' => $group_id,
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数

        ];
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_group_post') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        $group_info = $this->Group_model->get_group_infomation($group_id);
//        $this->response($group_info);

        if(empty($group_info)){
            $this->response(['error'=>'星球不存在'],404);
        }

        if($group_info['delete']){
            $this->response(['error'=>'星球已关闭'],410);
        }
        if($group_info['private']){
            $member=$this->Common_model->judge_group_user($group_id,$user_id);
            if(!$member&&$user_id!=$group_info['user_base_id']){
                $this->response(['error'=>'私密星球，帖子已隐藏'],403);
            }
        }

        $re['data'] = $this->Post_model->get_group_post($data);
        $re = $this->Post_model->get_image_url($re);              //解析帖子内容，获得帖子中包含的图片
        $re = $this->Post_model->delete_image_gif($re);           //删除帖子中gif格式的图片
        $re = $this->Post_model->post_image_limit($re);           //展示图片限制，目前是显示三张
        $re = $this->Post_model->delete_html_posts($re);          //从帖子内容去除 HTML 和 PHP 标记
        $re = $this->Post_model->post_text_limit($re);            //帖子内容长度显示，目前是300字符

        foreach($re['data'] as $key=>$value){
            $re['data'][$key] = [
                'post' => [
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'content'  => $value['content'],
//                    'lock'    => $value['lock'],
                    'digest'  => $value['digest']?TRUE:FALSE,
                    'sticky'  => $value['sticky']?TRUE:FALSE,
                    'create_time'   => $value['create_time'],
                    'approved'      => $value['approved']?TRUE:FALSE,
                    'approved_num'  =>$value['approved_num'],
                    'collected'     => $value['collected']?TRUE:FALSE,
                    'collected_num' => $value['collected_num'],
                    /*此处待修改*/       'replied'       => $value['replied']?TRUE:FALSE,
                    /*此处待修改*/       'replied_num'   => $value['replied_num'],
                    'image_url'         => $value['image'],
                ],
                'user' => [
                    'avatar_url' =>$value['profile_picture']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
                    'name'       =>$value['nickname'],
                    'id'         =>$value['user_base_id'],
                ],
            ];
        }

        //分页
        $all_num = $this->Post_model->get_group_post($data,TRUE);
        $offset = $data['offset'];
        $limit = $data['limit'];
        $page_count  = (ceil($all_num/$limit)-1);                   //比总页数小 1
        $finallyo = $page_count * $limit;
        $lasto = ($offset-$limit)>0?($offset-$limit):0;
        $nexto = ($offset+$limit)<$finallyo?($offset+$limit):$finallyo;
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                ''
            );

        $re['paging'] = [
            'first'=> "{$host}/groups/{$group_id}/posts?limit={$limit}&offset=0",
            'previous'=>"{$host}/groups/{$group_id}/posts?limit={$limit}&offset={$lasto}",
            'next'=> "{$host}/groups/{$group_id}/posts?limit={$limit}&offset={$nexto}",
            'final'=> "{$host}/groups/{$group_id}/posts?limit={$limit}&offset={$finallyo}"
        ];

        $this->response($re);

    }

    /**
     * 单个帖子的内容详情，不包括回复列表
     */
    public function post_content_get($group_id){
       /**
        * 输出调试
        * $this->output->enable_profiler(TRUE);
        * print_r($this->db->queries);
        */

        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        if(empty($jwt)){
            $user_id = NULL;
        }else{
            $token = $this->parsing_token($jwt);
            $user_id = $token->user_id;
        }

        //输入参数验证
        $data = array(
            'post_id' =>$group_id,
            'user_id' =>$user_id,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_post_base') == FALSE)
            $this->response(['error'=>validation_errors()],422);


        $model = $this->Post_model;
        $common_model = $this->Common_model;
        $post_info = $model->get_post_information($data['post_id']);
        if(empty($post_info)){
            $this->response(null,400,'帖子不存在！');
        }
        $rs = $model->get_post_base($data['post_id'],$data['user_id']);
        $rs['creator_id'] = $this->Group_model->get_group_infomation($rs['group_id'])['user_base_id'];
        $rs['creator_name'] = $this->User_model->get_user_information($rs['creator_id'])['nickname'];
        $group_id=$model->get_group_id($data['post_id']);
        $private_group = $common_model->judge_group_private($group_id);
        $rs['edit_right']=0;
        $rs['delete_right']=0;
        $rs['sticky_right']=0;
        $rs['lock_right']=0;
        $rs['code'] = 1;
        $msg = '查看帖子成功';
        $re['group'] = $common_model->judge_group_exist($group_id);
        $re['post'] = $common_model->judge_post_exist($data['post_id']);
        if(!$re['post']){
            $rs = null;
            $rs['code'] = 0;
            if($re['group']){
                $msg = "帖子已被删除，不可查看！";
            }else{
                $msg = "帖子所属星球已关闭，不可查看！";
            }
            $this->response($rs,404,$msg);
        }
        if ($data['user_id'] !=null){
            $creator= $common_model->judge_group_creator($group_id,$data['user_id']);
            $poster = $common_model->judge_post_creator($data['user_id'],$data['post_id']);
            $admin = $common_model->judge_admin($data['user_id']);
            if($poster)
            {
                $rs['edit_right']=1;
                $rs['delete_right']=1;
                $rs['lock_right']=1;
            }
            if($creator){
                $rs['delete_right']=1;
                $rs['sticky_right']=1;
                $rs['lock_right']=1;
            }
            if($admin){
                $rs['delete_right']=1;
                $rs['sticky_right']=1;
                $rs['lock_right']=1;
            }
        }
        $array = [
            'groups'=>[
                'group_id' =>$rs['group_id'],
                'g_name'   =>$rs['g_name'],
                'g_image'  =>$rs['g_image'],
                'g_introduction'=>$rs['g_introduction'],
                'creator_id'  =>$rs['creator_id'],
                'creator_name' =>$rs['creator_name'],
                'post_num'=>$this->Group_model->get_group_post_num($group_id),
                'user_num'=>$this->Group_model->get_group_user_num($group_id),
            ],
            'posts'=>[
                'post_id'=>$rs['post_id'],
                'p_title'=>$rs['p_title'],
                'p_text'=>$rs['p_text'],
                'create_time'=>$rs['create_time'],
                'sticky'=>$rs['sticky'],
                'lock'=>$rs['lock'],
                'approved'=>$rs['approved'],
                'approved_num'=>$rs['approved_num'],
                'collected'=>$rs['collected'],
                'collected_num'=>$rs['collected_num'],
                'p_image'=>$rs['p_image'],
            ],
            'users'=>[
                'user_id'=>$rs['user_id'],
                'user_name'=>$rs['user_name'],
                'profile_picture'=>$rs['profile_picture'],
            ],
            'rights'=>[
                'edit_right'=>$rs['edit_right'],
                'delete_right'=>$rs['delete_right'],
                'sticky_right'=>$rs['sticky_right'],
                'lock_right'=>$rs['lock_right'],
            ],
            'code'=>$rs['code'],
        ];
        if($private_group){
            if($data['user_id'] !=null){
                $groupuser = $common_model->check_group($data['user_id'],$group_id);
                $groupcreator = $common_model->judge_group_creator($group_id,$data['user_id']);
                if(empty($groupcreator)){
                    if(empty($groupuser)){
                        unset($array);
                        $array['code'] = 2;
                        $array['group_id'] = $group_id;
                        $msg = "未加入，不可查看私密帖子！";
                    }
                }
            }else{
                unset($array);
                $array['code'] = 2;
                $array['group_id'] = $group_id;
                $msg = "未登录，不可查看私密帖子！";
            }
        }
        $this->response($array,200,$msg);
    }
    /**
     * 单个帖子的回复详情，不包括帖子内容
     */
    public function get_post_reply(){
        $data = array(
            'post_id' =>$this->input->get('post_id'),
            'user_id' =>$this->input->get('user_id'),
            'pn'      =>$this->input->get('pn'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_post_base') == FALSE)
            $this->response(null,400,validation_errors());
        $data['pn'] = empty($data['pn'])?1:$data['pn'];
        $model = $this->Post_model;
        $common = $this->Common_model;
        $rs = $model->get_post_reply($data['post_id'],$data['pn'],$data['user_id']);
        $group_id = $model->get_post_information($data['post_id'])['group_base_id'];
        $sqlb = $common->judge_group_creator($group_id,$data['user_id']);
        $sqld = $common->judge_admin($data['user_id']);
        $sqle = $common->judge_post_creator($data['user_id'],$data['post_id']);
        foreach ($rs['reply'] as $key => $value) {
            $sqlc = $common->judge_post_reply_user($data['user_id'],$data['post_id'],$value['p_floor']);
            if ($sqlc||$sqlb||$sqld||$sqle) {
                $rs['reply']["$key"]['delete_right']=1;
            }else{
                $rs['reply']["$key"]['delete_right']=0;
            }
        }
        $rs = $common->delete_html_reply($rs);
        $this->response($rs,200,$msg='帖子回复显示成功');
    }
    /**
     * 回复帖子
     */
    public function post_reply(){
        $data = array(
            'post_base_id' =>$this->input->post('post_id'),
            'user_base_id' =>$this->input->post('user_id'),
            'text'  =>$this->input->post('p_text'),
            'reply_floor'=>$this->input->post('reply_floor')
        );
        if ($this->form_validation->run('post_reply') == FALSE)
            $this->response(null,400,validation_errors());
        $exist =$this->Common_model->judge_post_exist($data['post_base_id']);
        $lock=$this->Common_model->judge_post_lock($data['post_base_id']);
        if($exist&&!$lock) {
            $data = $this->Post_model->post_reply($data);
            $msg='回复成功';
            $rs = array(
                'code'=>1,
                'reply_page'=>$this->Common_model->get_post_reply_page($data['post_base_id'],$data['reply_floor']),
                'post_id'=>$data['post_base_id'],
                'user_id'=>$data['user_base_id'],
                'reply_id'=>$data['reply_id'],
                'p_floor'=>$data['floor'],
                'p_text'=>$data['text'],
                'create_time'=>$data['create_time'],
                'user_name'=>$this->User_model->get_user_information($data['user_base_id'])['nickname'],
                'reply_user_name'=>$this->User_model->get_user_information($data['reply_id'])['nickname'],
                'page'=>$this->Common_model->get_post_reply_page($data['post_base_id'],$data['floor']),
            );
            $this->Post_model->post_reply_message($data);
        }else{
            $msg='帖子不存在或者被锁定';
            $rs['code'] = 0;
        }
        $this->response($rs,200,$msg);
    }
    /**
     * 编辑帖子
     */
    public function edit_post(){
        $access_token = $this->input->request_headers();
        if(empty($access_token['Access-Token'])){
            $this->response(NULL,400,'没有获取到Access-Token，请尝试重新登录！');
        }
//        $this->response($access_token);

        $re = $this->judge_authority1($access_token['Access-Token'],$this->input->post('post_id'));
        $this->form_validation->reset_validation();
        $this->form_validation->set_message('required', '{field} 参数是必填选项.');
        $data = array(
            'post_id' =>$re['post_id'],
            'user_id' =>$re['user_id'],
            'text'  =>$this->input->post('p_text'),
            'title'=>$this->input->post('p_title')
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('edit_post') == FALSE)
        {
            $this->response(null,400,validation_errors());
        }
        if($re['edit_right']===1){
            $msg='编辑成功';
            $rs['code'] = 1;
            $rs['post_id']=$data['post_id'];
            $this->Post_model->edit_post($data);
        }else{
            $msg='您没有权限操作！';
            $rs['code'] = 0;
        }
        $this->response($rs,200,$msg);
    }



    /**
     * 置顶帖子
     * @desc 帖子置顶
     * @return int code 操作码，2表示取消置顶成功，1表示置顶成功，0表示操作失败
     * @return string msg 提示信息
     */
    public function sticky_post(){
        $access_token = $this->input->request_headers();
        if(empty($access_token['Access-Token'])){
            $this->response(NULL,400,'没有获取到Access-Token，请尝试重新登录！');
        }
        $re = $this->judge_authority1($access_token['Access-Token'],$this->input->get('post_id'));
        if($re['sticky_right']===1){
            $rs = $this->Post_model->sticky_post();
        }else{
            $rs['code'] = 0;
            $rs['msg'] = "您没有操作权限!";
        }
        $this->response(['code'=>$rs['code']],200,$rs['msg']);
    }


    /**
     * 删除帖子
     * @desc 删除帖子
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
    public function delete_post(){
        $access_token = $this->input->request_headers();
        if(empty($access_token['Access-Token'])){
            $this->response(NULL,400,'没有获取到Access-Token，请尝试重新登录！');
        }
        $re = $this->judge_authority1($access_token['Access-Token'],$this->input->get('post_id'));
        if($re['delete_right']===1){
            $rs = $this->Post_model->delete_post($re);
            if($rs)
            {
                $data['code'] = 1;
                $msg = "成功删除帖子!";
            }
            else
            {
                $data['code'] = 0;
                $msg = "操作过于频繁!";
            }
        }else{
            $data['code'] = 0;
            $msg = "仅星球创建者和发帖者和管理员能删除帖子!";
        }
        $this->response($data,200,$msg);
    }
//    public function delete_post(){
//        $data=array(
//            'user_id'=>$this->input->get('user_id'),
//            'post_id'=>$this->input->get('post_id'),
//        );
//        $sqla=$this->Post_model->get_group_id($data['post_id']);
//        $sqlb=$this->User_model->judge_create($data['user_id'],$sqla);
//        $sqlc=$this->Post_model->judge_poster($data['user_id'],$data['post_id']);
//        $sqld=$this->User_model->judge_admin($data['user_id']);
//        if($sqlb||$sqlc||$sqld){
//            $re=$this->Post_model->delete_post($data);
//            $msg='成功删除帖子';
//        }else{
//            $re['code']=0;
//            $msg='仅星球创建者和发帖者和管理员能删除帖子!';
//        }
//        $this->response($re,200,$msg);
//    }
    private function parsing_token($jwt)
    {
        try{
            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
            return $token;
        }
        catch(InvalidArgumentException $e)
        {
            $this->response(null,400,'token解析失败，原因：'.$e->getMessage());
        }
        catch(UnexpectedValueException $e)
        {
            $this->response(null,400,'token解析失败，原因：'.$e->getMessage());
        }
    }
    private function judge_authority1($jwt,$post_id){
        $token = $this->parsing_token($jwt);
        $data = array(
            'user_id' => $token->user_id,
            'post_id' => $post_id,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(null,400,validation_errors());
        $creator= $this->Common_model->judge_group_creator($data['post_id'],$data['user_id']);
        $poster = $this->Common_model->judge_post_creator($data['user_id'],$data['post_id']);
        $admin = $this->Common_model->judge_admin($data['user_id']);
        $rs = [
            'edit_right'=>0,
            'delete_right'=>0,
            'sticky_right'=>0,
            'lock_right'=>0,
            'user_id'=>$data['user_id'],
            'post_id'=>$data['post_id'],
        ];
        if($poster)
        {
            $rs['edit_right']=1;
            $rs['delete_right']=1;
            $rs['lock_right']=1;
        }
        if($creator){
            $rs['delete_right']=1;
            $rs['sticky_right']=1;
            $rs['lock_right']=1;
        }
        if($admin){
            $rs['delete_right']=1;
            $rs['sticky_right']=1;
            $rs['lock_right']=1;
        }
        return $rs;
    }


    private function judge_authority($jwt){
        $token = $this->parsing_token($jwt);
        $data = array(
            'user_id' => $token->user_id,
            'post_id' => $token->post_id,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(null,400,validation_errors());
        $creator= $this->Common_model->judge_group_creator($data['post_id'],$data['user_id']);
        $poster = $this->Common_model->judge_post_creator($data['user_id'],$data['post_id']);
        $admin = $this->Common_model->judge_admin($data['user_id']);
        $rs = [
            'edit_right'=>0,
            'delete_right'=>0,
            'sticky_right'=>0,
            'lock_right'=>0,
            'user_id'=>$data['user_id'],
            'post_id'=>$data['post_id'],
        ];
        if($poster)
        {
            $rs['edit_right']=1;
            $rs['delete_right']=1;
            $rs['lock_right']=1;
        }
        if($creator){
            $rs['delete_right']=1;
            $rs['sticky_right']=1;
            $rs['lock_right']=1;
        }
        if($admin){
            $rs['delete_right']=1;
            $rs['sticky_right']=1;
            $rs['lock_right']=1;
        }
        return $rs;
    }

    /**
     * 锁定帖子
     * @desc 锁定帖子
     * @return int code 操作码，2表示取消锁定成功，1表示锁定成功，0表示操作失败
     * @return string re 提示信息
     */
    public function lock_post(){
        $access_token = $this->input->request_headers();
        if(empty($access_token['Access-Token'])){
            $this->response(NULL,400,'没有获取到Access-Token，请尝试重新登录！');
        }
        $re = $this->judge_authority1($access_token['Access-Token'],$this->input->get('post_id'));
        if($re['lock_right']===1){
            $rs = $this->Post_model->lock_post();
        }else{
            $rs['code'] = 0;
            $rs['msg'] = "您没有操作权限!";
        }
        $this->response(['code'=>$rs['code']],200,$rs['msg']);
    }

    /**
     * 解锁帖子             ***已合并***
     * @desc 解锁帖子
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
//    public function unlock_post(){
//        $access_token = $this->input->request_headers();
//        if(empty($access_token['Access-Token'])){
//            $this->response(NULL,400,'没有获取到Access-Token，请尝试重新登录！');
//        }
//        $re = $this->judge_authority1($access_token['Access-Token'],$this->input->get('post_id'));
//        if($re['lock_right']===1){
//            $rs = $this->Post_model->unlock_post();
//            if($rs)
//            {
//                $data['code'] = 1;
//                $msg = "解除锁定帖子成功!";
//            }
//            else
//            {
//                $data['code'] = 0;
//                $msg = "操作过于频繁!";
//            }
//        }else{
//            $data['code'] = 0;
//            $msg = "您没有操作权限!";
//        }
//        $this->response($data,200,$msg);
//    }


    /**
     * 收藏帖子
     * @desc 收藏帖子
     * @return int code 操作码，1表示收藏成功，0表示操作失败，2表示取消收藏成功
     * @return string re 提示信息
     */
    public function collect_post_put(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'post_id'=>$this->input->get('post_id'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(null,400,validation_errors());
        $post_exist = $this->Common_model->judge_post_exist($data['post_id']);
        $exist=$this->Common_model->ifexist_collect_post($data);
        if($exist){
            $rs=$this->Post_model->update_collect_post($data,$post_exist);
        }else{
            if($post_exist&&$this->Post_model->collect_post($data)){
                $rs['code'] = 1;
                $rs['msg'] = '收藏成功';
            }else{
                $rs['code'] = 0;
                $rs['msg'] = '操作失败，可能帖子不存在';
            }
        }
        $this->response(['code'=>$rs['code']],200,$rs['msg']);
    }

    /**
     * 获取收藏的帖子
     * @desc 获取收藏的帖子
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
    public function get_collect_post(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'page'=>$this->input->get('page'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        $re=$this->Post_model->get_collect_post($data);
        //$re = $this->Post_model->get_image_url($re);
        //$re = $this->Post_model->delete_image_gif($re);
        //$re = $this->Post_model->post_image_limit($re);
        $re = $this->Post_model->delete_html_posts($re);
        $re = $this->Post_model->post_text_limit($re);
        $this->response($re,200,null);
    }

    /**
     * 删除收藏帖子   已合并到收藏帖子接口
     * @desc 删除收藏帖子
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
//    public function delete_collect_post(){
//        $data=array(
//            'user_id'=>$this->input->get('user_id'),
//            'post_id'=>$this->input->get('post_id'),
//        );
//        $rs=$this->Post_model->delete_collect_post($data);
//        if($rs){
//            $info['code']=1;
//            $msg="删除收藏成功！";
//        }else{
//            $info['code']=0;
//            $msg="操作过于频繁！";
//        }
//        $this->response($info,200,$msg);
//    }

	

    /**
     * 删除帖子回复
     * @desc 删除帖子回复
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
    public function delete_post_reply(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'post_id'=>$this->input->get('post_id'),
            'floor'=>$this->input->get('floor'),
        );
        $sqla=$this->Post_model->get_group_id($data['post_id']);
        $sqlb=$this->User_model->judge_create($data['user_id'],$sqla);
        $sqlc=$this->Post_model->judge_post_reply_user($data['user_id'],$data['post_id'],$data['floor']);
        $sqld=$this->User_model->judge_admin($data['user_id']);
        if($sqlb||$sqlc||$sqld){
            $re=$this->Post_model->delete_post_reply($data);
            $msg='成功删除帖子回复';
        }else{
            $re['code']=0;
            $msg='仅星球创建者和回帖者和管理员能删除帖子!';
        }
        $this->response($re,200,$msg);
    }

}
