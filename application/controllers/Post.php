<?php



class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->model('User_model');
        $this->load->model('Group_model');
        $this->load->model('Common_model');
        $this->load->helper(array('form', 'url','url_helper'));
        $this->load->library('form_validation');
        $this->form_validation->set_message('required', '{field} 参数是必填选项.');
        $this->form_validation->set_message('min_length', '{field} 参数长度不小于{param}.');
        $this->form_validation->set_message('max_length', '{field} 参数长度不大于{param}.');
    }
    /**
     * @param $data
     * @param int $ret
     * @param null $msg
     * 返回JSON数据到前端
     */
    public function response($data,$ret=200,$msg=null){
        $response=array('ret'=>$ret,'data'=>$data,'msg'=>$msg);
        $this->output
            ->set_status_header($ret)
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: 0')
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))
            ->_display();
        exit;
    }

    /**
     * 单个帖子的内容详情，不包括回复列表
     */
    public function get_post_base(){
        //$this->output->enable_profiler(TRUE);
        //print_r($this->db->queries);
        $data = array(
            'post_id' =>$this->input->get('post_id'),
            'user_id' =>$this->input->get('user_id'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_post_base') == FALSE)
            $this->response(null,400,validation_errors());
        $model = $this->Post_model;
        $common_model = $this->Common_model;
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
            unset($rs);
            $rs['code'] = 0;
            if($re['group']){
                $msg = "帖子已被删除，不可查看！";
            }else{
                $msg = "帖子所属星球已关闭，不可查看！";
            }
            $this->response($rs,200,$msg);
        }
        if($private_group){
            if($data['user_id'] !=null){
                $groupuser = $common_model->check_group($data['user_id'],$group_id);
                $groupcreator = $common_model->judge_group_creator($group_id,$data['user_id']);
                if(empty($groupcreator)){
                    if(empty($groupuser)){
                        unset($rs);
                        $rs['code'] = 2;
                        $rs['group_id'] = $group_id;
                        $msg = "未加入，不可查看私密帖子！";
                    }
                }
            }else{
                unset($rs);
                $rs['code'] = 2;
                $rs['group_id'] = $group_id;
                $msg = "未登录，不可查看私密帖子！";
            }
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
        $re = $this->judge_authority($this->input->post('token'));
        $data = array(
            'post_id' =>$re['post_id'],
            'user_id' =>$re['user_id'],
            'text'  =>$this->input->post('p_text'),
            'title'=>$this->input->post('p_title')
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('edit_post') == FALSE)
            $this->response(null,400,validation_errors());
        if($re['edit_right']===1){
            $msg='编辑成功';
            $rs['code'] = 1;
            $rs['post_id']=$data['post_base_id'];
            $this->Post_model->edit_post($data);
        }else{
            $msg='您没有权限操作！';
            $rs['code'] = 0;
        }
        $this->response($rs,200,$msg);
    }

    /**
     * 主页
     * @desc 主页面帖子显示
     * @return int posts.postID 帖子ID
     * @return string posts.title 标题
     * @return string posts.text 内容
     * @return string posts.createTime 发帖时间
     * @return string posts.nickname 发帖人
     * @return int posts.groupID 星球ID
     * @return int posts.lock 是否锁定
     * @return int posts.approved 是否点赞(0未点赞，1已点赞)
     * @return int posts.approvednum 点赞数
     * @return string posts.groupName 星球名称
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function get_index_post(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'page'=>$this->input->get('pn'),
        );
        if(!empty($data['user_id'])){
            $re = $this->Post_model->get_mygroup_post($data['user_id'],$data['page']);
            //$re['user_name']=$this->Post_model->get_user($data['user_id']);
        }
        if(empty($re['posts'])){
        $re=$this->Post_model->get_index_post($data);
        }
        $re=$this->Post_model->get_image_url($re);
        $re=$this->Post_model->delete_image_gif($re);
        $re=$this->Post_model->post_image_limit($re);
        $re=$this->Post_model->delete_html_posts($re);
        $re=$this->Post_model->post_text_limit($re);
        foreach($re['posts'] as $key=>$value){
            $re['posts'][$key] = [
                'posts' => [
                    'post_id' => $value['post_id'],
                    'p_title' => $value['p_title'],
                     'p_text'  => $value['p_text'],
                    'lock'    => $value['lock'],
                    'create_time' => $value['create_time'],
                    'approved'=> $value['approved'],
                     'approved_num'  =>$value['approved_num'],
                    'collected' => $value['collected'],
                    'collected_num'     => $value['collected_num'],
                    'replied'   => $value['replied'],
                    'replied_num'   => $value['replied_num'],
                    'image'      => $value['image'],
                ],
                'users' => [
                    'profile_picture' =>$value['profile_picture'],
                    'user_name'       =>$value['user_name'],
                    'user_id'         =>$value['user_id'],
                ],
                'groups' => [
                    'group_id'  => $value['group_id'],
                    'g_name'    => $value['g_name'],
                ],
            ];
        }

        $this->response($re,200,null);
    }


    /**
     * 我的星球
     * @desc 我的星球页面帖子显示
     * @return int posts.postID 帖子ID
     * @return string posts.title 标题
     * @return string posts.text 内容
     * @return string posts.createTime 发帖时间
     * @return string posts.nickname 发帖人
     * @return int posts.groupID 星球ID
     * @return string posts.groupName 星球名称
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     * @return string user_name 用户名

    public function get_mygroup_post(){
        $user_id=$this->input->get('user_id');
        $pn=$this->input->get('pn');
        $data['user_id'] = $user_id;
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        $data = $this->Post_model->get_mygroup_post($user_id,$pn);
        $data = $this->Post_model->get_image_url($data);
        $data = $this->Post_model->delete_image_gif($data);
        $data = $this->Post_model->post_image_limit($data);
        $data = $this->Post_model->delete_html_posts($data);
        $data = $this->Post_model->post_text_limit($data);
        foreach($data['posts'] as $key=>$value){
            $data['posts'][$key] = [
                'posts' => [
                    'post_id' => $value['post_id'],
                    'p_title' => $value['p_title'],
                    'p_text'  => $value['p_text'],
                    'lock'    => $value['lock'],
                    'create_time' => $value['create_time'],
                    'approved'=> $value['approved'],
                    'approved_num'  =>$value['approved_num'],
                    'collected' => $value['collected'],
                    'collected_num'     => $value['collected_num'],
                    'replied'   => $value['replied'],
                    'replied_num'   => $value['replied_num'],
                    'image'      => $value['image'],
                ],
                'users' => [
                    'profile_picture' =>$value['profile_picture'],
                    'user_name'       =>$value['user_name'],
                    'user_id'         =>$value['user_id'],
                ],
                'groups' => [
                    'group_id'  => $value['group_id'],
                    'g_name'    => $value['g_name'],
                ],
            ];
        }
        $data['user_name']=$this->Post_model->get_user($user_id);

        $this->response($data,200,null);
    }
     */



    /**
     * 每个星球页面帖子显示
     * @desc 星球页面帖子显示
     * @return int creatorID 星球创建者ID
     * @return string creatorName 星球创建者名称
     * @return int groupID 星球ID
     * @return string groupName 星球名称
     * @return int post.digest 加精
     * @return string posts.title 标题
     * @return string posts.text 内容
     * @return string posts.createTime 发帖时间
     * @return int posts.postID 帖子ID
     * @return string posts.nickname 发帖人
     * @return int posts.sticky 是否置顶（0为未置顶，1置顶）
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     * @return int identity 用户身份(01为创建者，02为成员，03非成员)
     * @return int private 是否私密(0为否，1为私密)
     */
    public function get_group_post(){
        $group_id=$this->input->get('group_id');
        $user_id=$this->input->get('user_id');
        $page=$this->input->get('pn');
        $data['group_id']=$group_id;
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_group_post') == FALSE)
            $this->response(null,400,validation_errors());
        $re = $this->Group_model->get_group_infomation($group_id);
        if(empty($re['id'])){
            $this->response(null,400,'星球不存在！');
        }
        if(empty($re['g_image'])){
            $re['g_image'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
        }
        $data = [
            'group_id'=>$re['id'],
            'g_name'=>$re['name'],
            'g_introduction'=>$re['g_introduction'],
            'g_image'=>$re['g_image'],
            'private'=>$re['private'],
            'creator_id'=>$re['user_base_id'],
            'creator_name'=>$this->User_model->get_user_information($re['user_base_id'])['nickname'],
            'post_num'=>$this->Group_model->get_group_post_num($group_id),
            'user_num'=>$this->Group_model->get_group_user_num($group_id),

        ];
        $user=$this->Common_model->judge_group_user($group_id,$user_id);
        $creator=$this->Common_model->judge_group_creator($group_id,$user_id);
        $applicate=$this->Common_model->judge_user_application($user_id,$group_id);
        if($re['delete']){
            $data['posts']=array();
            $data['pageCount']=1;
            $data['currentPage']=1;
            $this->response($data,200,'星球已关闭，不显示帖子');
        }
        if(empty($user)&&empty($creator)){
            $data['identity']='03';
            $data['posts']=array();
            $msg = '您还没有加入该星球';
            if($data['private']==1){
                if(!empty($applicate)){
                    $data['identity']='04';
                    $msg = '申请中，请等待星球主人审核！';
                }
                $data['posts']=array();
                $data['pageCount']=1;
                $data['currentPage']=1;
                $this->response($data,200,$msg);
            }
        }elseif (!empty($user)) {
            $data['identity']='02';
        }elseif (!empty($creator)) {
            $data['identity']='01';
        }
        $data =array_merge($data,$this->Post_model->get_group_post($group_id,$page,$user_id));
        $data = $this->Post_model->get_image_url($data);
        $data = $this->Post_model->delete_image_gif($data);
        $data = $this->Post_model->post_image_limit($data);
        $data = $this->Post_model->delete_html_posts($data);
        $data = $this->Post_model->post_text_limit($data);

        foreach($data['posts'] as $key=>$value){
            $data['posts'][$key] = [
                'posts' => [
                    'post_id' => $value['post_id'],
                    'p_title' => $value['p_title'],
                    'p_text'  => $value['p_text'],
                    'lock'    => $value['lock'],
                    'digest'  => $value['digest'],
                    'sticky'  => $value['sticky'],
                    'create_time'   => $value['create_time'],
                    'approved'      => $value['approved'],
                    'approved_num'  =>$value['approved_num'],
                    'collected'     => $value['collected'],
                    'collected_num' => $value['collected_num'],
                    'replied'       => $value['replied'],
                    'replied_num'   => $value['replied_num'],
                    'image'         => $value['image'],
                ],
                'users' => [
                    'profile_picture' =>$value['profile_picture'],
                    'user_name'       =>$value['user_name'],
                    'user_id'         =>$value['user_id'],
                ],
            ];
        }
        $this->response($data,200,'浏览帖子成功');

    }

    /**
     * 置顶帖子
     * @desc 帖子置顶
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string msg 提示信息
     */
    public function sticky_post(){
        $re = $this->judge_authority($this->input->get('token'));
        if($re['sticky_right']===1){
            $rs = $this->Post_model->post_sticky();
            if($rs)
            {
                $data['code'] = 1;
                $msg = "置顶帖子成功!";
            }
            else
            {
                $data['code'] = 0;
                $msg = "操作过于频繁!";
            }
        }else{
            $data['code'] = 0;
            $msg = "您没有操作权限!";
        }
        $this->response($data,200,$msg);
    }

    /**
     * 取消置顶帖子
     * @desc 帖子取消置顶
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string msg 提示信息
     */
    public function unsticky_post(){
        $re = $this->judge_authority($this->input->get('token'));
        if($re['sticky_right']===1){
            $rs = $this->Post_model->post_unsticky();
            if($rs)
            {
                $data['code'] = 1;
                $msg = "取消置顶帖子成功!";
            }
            else
            {
                $data['code'] = 0;
                $msg = "操作过于频繁!";
            }
        }else{
            $data['code'] = 0;
            $msg = "您没有操作权限!";
        }
        $this->response($data,200,$msg);
    }


    /**
     * 删除帖子
     * @desc 删除帖子
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
    public function delete_post(){
        $re = $this->judge_authority($this->input->get('token'));
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
    private function judge_authority($jwt){
        try{
            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
        }
        catch(InvalidArgumentException $e)
        {
            $this->response(null,400,'token解析失败，原因：'.$e->getMessage());
        }
        catch(UnexpectedValueException $e)
        {
            $this->response(null,400,'token解析失败，原因：'.$e->getMessage());
        }
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
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
    public function lock_post(){
        $re = $this->judge_authority($this->input->get('token'));
        if($re['lock_right']===1){
            $rs = $this->Post_model->lock_post();
            if($rs)
            {
                $data['code'] = 1;
                $msg = "锁定帖子成功!";
            }
            else
            {
                $data['code'] = 0;
                $msg = "操作过于频繁!";
            }
        }else{
            $data['code'] = 0;
            $msg = "您没有操作权限!";
        }
        $this->response($data,200,$msg);
    }

    /**
     * 解锁帖子
     * @desc 解锁帖子
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
    public function unlock_post(){
        $re = $this->judge_authority($this->input->get('token'));
        if($re['lock_right']===1){
            $rs = $this->Post_model->unlock_post();
            if($rs)
            {
                $data['code'] = 1;
                $msg = "解除锁定帖子成功!";
            }
            else
            {
                $data['code'] = 0;
                $msg = "操作过于频繁!";
            }
        }else{
            $data['code'] = 0;
            $msg = "您没有操作权限!";
        }
        $this->response($data,200,$msg);
    }


    /**
     * 收藏帖子
     * @desc 收藏帖子
     * @return int code 操作码，1表示收藏成功，0表示操作失败，2表示取消收藏成功
     * @return string re 提示信息
     */
    public function collect_post(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'post_id'=>$this->input->get('post_id'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(null,400,validation_errors());
        $exist=$this->Common_model->ifexist_collect_post($data);
        if($exist){
            $rs=$this->Post_model->update_collect_post($data);

        }else{
            if($this->Post_model->collect_post($data)){
                $rs['code'] = 1;
                $rs['msg'] = '收藏成功';
            }else{
                $rs['code'] = 0;
                $rs['msg'] = '操作失败';
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
     * 点赞帖子
     * @desc 点赞帖子
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string re 提示信息
     */
    public function approve_post(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'post_id'=>$this->input->get('post_id'),
            'floor'=>$this->input->get('floor'),
        );
        $rs=$this->Post_model->get_approve_post($data);
        if($rs){
            $rs=$this->Post_model->update_approve_post($data);
        }else{
            $rs=$this->Post_model->add_approve_post($data);
        }
        $this->response($rs,200,null);
    }

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
