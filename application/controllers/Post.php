<?php



class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->model('Common_model');
        $this->load->helper('url_helper');
    }
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
    public function get_index_post($user_id,$page){
        $data=array(
            'user_id'=>$user_id,
            'page'=>$page,
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
    public function get_mygroup_post($user_id,$page){
        $data   = array();

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
    public function get_group_post($group_id,$page,$user_id=null){
        $data   = array();

        $data['creatorID']=$this->Post_model->get_creater_id($group_id)['user_base_id'];
        $creatorName=$this->Post_model->get_creator($group_id);
        $data['creatorName']=$creatorName;
        $data['groupID']=$group_id;
        $rs = $this->Common_model->judge_group_exist($data['groupID']);
        $data['groupName']=$this->Common_model->get_group_name($group_id);
        $private=$this->Common_model->judge_group_private($group_id);
        $data['private']=$private;
        $user=$this->Common_model->judge_group_user($group_id,$user_id);
        $creator=$this->Common_model->judge_group_creator($group_id,$user_id);
        $applicate=$this->Common_model->judge_user_application($user_id,$group_id);
        if(empty($rs)){
            $data['posts']='星球已关闭，不显示帖子';
            $data['pageCount']=1;
            $data['currentPage']=1;
            return $data;
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
                return $data;
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




}