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
        $this->load->helper('url_helper');
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
        $data = array(
            'post_id' =>$this->input->get('post_id'),
            'user_id' =>$this->input->get('user_id'),
        );
        $model = $this->Post_model;
        $common_model = $this->Common_model;
        $rs = $model->get_post_base($data['post_id'],$data['user_id']);
        $rs[0]['collect']=$common_model->judge_collect_post($data['post_id'],$data['user_id']);
        $group_id=$model->get_group_id($data['post_id']);
        $private_group = $common_model->judge_group_private($group_id);
        $rs[0]['edit_right']=0;
        $rs[0]['delete_right']=0;
        $rs[0]['sticky_right']=0;
        $rs[0]['lock_right']=0;
        $rs[0]['code'] = 1;
        $msg = '查看帖子成功';
        $re['group'] = $common_model->judge_group_exist($group_id);
        $re['post'] = $common_model->judge_post_exist($data['post_id']);
        if(!$re['post']){
            unset($rs);
            $rs[0]['code'] = 0;
            if($re['group']){
                $msg = "帖子已被删除，不可查看！";
            }else{
                $msg = "帖子所属星球已关闭，不可查看！";
            }
            $this->response($rs[0],200,$msg);
        }
        if($private_group){
            if($data['user_id'] !=null){
                $groupuser = $common_model->check_group($data['user_id'],$group_id);
                $groupcreator = $common_model->judge_group_creator($group_id,$data['user_id']);
                if(empty($groupcreator)){
                    if(empty($groupuser)){
                        unset($rs);
                        $rs[0]['code'] = 2;
                        $rs[0]['group_id'] = $group_id;
                        $msg = "未加入，不可查看私密帖子！";
                    }
                }
            }else{
                unset($rs);
                $rs[0]['code'] = 2;
                $rs[0]['group_id'] = $group_id;
                $msg = "未登录，不可查看私密帖子！";
            }
        }
        if ($data['user_id'] !=null){
            $creater= $common_model->judge_group_creator($group_id,$data['user_id']);
            $poster = $common_model->judge_post_creator($data['user_id'],$data['post_id']);
            $admin = $common_model->judge_admin($data['user_id']);
            if($poster)
            {
                $rs[0]['edit_right']=1;
                $rs[0]['delete_right']=1;
                $rs[0]['lock_right']=1;
            }
            if($creater){
                $rs[0]['delete_right']=1;
                $rs[0]['sticky_right']=1;
                $rs[0]['lock_right']=1;
            }
            if($admin){
                $rs[0]['delete_right']=1;
                $rs[0]['sticky_right']=1;
                $rs[0]['lock_right']=1;
            }
        }
        $this->response($rs[0],200,$msg);
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
            'post_base_id' =>$this->input->get('post_id'),
            'user_base_id' =>$this->input->get('user_id'),
            'text'  =>$this->input->get('p_text'),
            'reply_floor'=>$this->input->get('reply_floor')
        );
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
        $data = array(
            'post_base_id' =>$this->input->get('post_id'),
            'user_base_id' =>$this->input->get('user_id'),
            'text'  =>$this->input->get('p_text'),
            'title'=>$this->input->get('p_title')
        );
        $poster = $this->Common_model->judge_post_creator($data['user_base_id'],$data['post_base_id']);
        if($poster){
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
     * @return date posts.createTime 发帖时间
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
        $re=$this->Post_model->get_index_post($data);
        $re=$this->Post_model->get_image_url($re);
        $re=$this->Post_model->delete_image_gif($re);
        $re=$this->Post_model->post_image_limit($re);
        $re=$this->Post_model->delete_html_posts($re);
        $re=$this->Post_model->post_text_limit($re);

        $this->response($re,200,null);
    }


    /**
     * 我的星球
     * @desc 我的星球页面帖子显示
     * @return int posts.postID 帖子ID
     * @return string posts.title 标题
     * @return string posts.text 内容
     * @return date posts.createTime 发帖时间
     * @return string posts.nickname 发帖人
     * @return int posts.groupID 星球ID
     * @return string posts.groupName 星球名称
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     * @return string user_name 用户名
     */
    public function get_mygroup_post(){
        $data   = array();
        $user_id=$this->input->get('user_id');
        $page=$this->input->get('pn');

        $data = $this->Post_model->get_mygroup_post($user_id,$page);
        $data = $this->Post_model->get_image_url($data);
        $data = $this->Post_model->delete_image_gif($data);
        $data = $this->Post_model->post_image_limit($data);
        $data = $this->Post_model->delete_html_posts($data);
        $data = $this->Post_model->post_text_limit($data);
        $data['user_name']=$this->Post_model->get_user($user_id);

        $this->response($data,200,null);
    }



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
     * @return date posts.createTime 发帖时间
     * @return int posts.postID 帖子ID
     * @return string posts.nickname 发帖人
     * @return int posts.sticky 是否置顶（0为未置顶，1置顶）
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     * @return int identity 用户身份(01为创建者，02为成员，03非成员)
     * @return int private 是否私密(0为否，1为私密)
     */
    public function get_group_post(){
        $data   = array();
        $group_id=$this->input->get('group_id');
        $user_id=$this->input->get('user_id');
        $page=$this->input->get('pn');

        $data['creator_id']=$this->Post_model->get_creater_id($group_id)['user_base_id'];
        $creatorName=$this->Post_model->get_creator($group_id);
        $data['creator_name']=$creatorName;
        $data['group_id']=$group_id;
        $rs = $this->Common_model->judge_group_exist($data['group_id']);
        $data['g_name']=$this->Common_model->get_group_name($group_id);
        $private=$this->Common_model->judge_group_private($group_id);
        $data['private']=$private;
        $user=$this->Common_model->judge_group_user($group_id,$user_id);
        $creator=$this->Common_model->judge_group_creator($group_id,$user_id);
        $applicate=$this->Common_model->judge_user_application($user_id,$group_id);
        if(empty($rs)){
            $data['posts']='星球已关闭，不显示帖子';
            $data['pageCount']=1;
            $data['currentPage']=1;
            $this->response($data,200,null);
        }
        if(empty($user)&&empty($creator)){
            $data['identity']='03';
            $data['posts']=array();
            if($private==1){
                if(!empty($applicate)){
                    $data['identity']='04';
                }
                $data['posts']=array();
                $data['pageCount']=1;
                $data['currentPage']=1;
                $this->response($data,200,null);
            }
        }elseif (!empty($user)) {
            $data['identity']='02';
        }elseif (!empty($creator)) {
            $data['identity']='01';
        }
        $data =array_merge($data,$this->Post_model->get_group_post($group_id,$page));
        $data = $this->Post_model->get_image_url($data);
        $data = $this->Post_model->delete_image_gif($data);
        $data = $this->Post_model->post_image_limit($data);
        $data = $this->Post_model->delete_html_posts($data);
        $data = $this->Post_model->post_text_limit($data);

        $this->response($data,200,null);

    }

    /**
     * 置顶帖子
     * @desc 帖子置顶
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string msg 提示信息
     */
    public function post_sticky(){        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $rs = array();


        $rs = $this->Post_model->post_sticky();
        if($rs)
        {
            $data['code'] = 1;
            $msg = "操作成功";
        }
        else
        {
            $data['code'] = 0;
            $msg = "操作失败";
        }
        $this->response($data,200,$msg);
    }

    /**
     * 取消置顶帖子
     * @desc 帖子取消置顶
     * @return int code 操作码，1表示操作成功，0表示操作失败
     * @return string msg 提示信息
     */
    public function post_unsticky(){        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $rs = array();


        $rs = $this->Post_model->post_unsticky();
        if($rs)
        {
            $data['code'] = 1;
            $msg = "操作成功";
        }
        else
        {
            $data['code'] = 0;
            $msg = "操作失败";
        }
        $this->response($data,200,$msg);
    }

}