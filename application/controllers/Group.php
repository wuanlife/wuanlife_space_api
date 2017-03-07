<?php



class Group extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Group_model');
        $this->load->model('Common_model');
        $this->load->model('User_model');
        $this->load->model('Post_model');
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



    public function create($user_id,$g_name,$g_image,$g_introduction,$private){
        $private=$this->Common_model->judgePrivate($private);
        $data=array(
            'user_id'=>$user_id,
            'g_name'=>$g_name,
            'g_image'=>$g_image,
            'g_introduction'=>$g_introduction,
            'private'=>$private,
        );
        $msg=null;
        $re=$this->Group_model->create($data);
        $this->response($re,200,$msg);

    }
    public function lists(){
        $model = $this->Group_model;
        $pn = $this->input->get('pn');
        $all_num      = $model->get_all_group_num();              //总条
        $page_num     = 20;                                       //每页条数
        $pageCount =ceil($all_num/$page_num);                //总页数
        if ($pageCount == 0){
            $pageCount =1;
        }
        if($pn > $pageCount){
            $pn = $pageCount;
        }
        $pn         =empty($pn)?1:$pn;                    //当前页数
        $pn         =(int)$pn;                              //安全强制转换
        $limit_st     =($pn-1)*$page_num;                     //起始数
        $re =  $model->lists($limit_st,$page_num);
        $rs['lists']=$this->Common_model->judge_image_exist($re);
        $rs['pageCount']  = $pageCount;
        $rs['currentPage'] = $pn;
        if(empty($re)){
            $msg = '暂无星球';
        }else{
            $msg = '获取星球列表成功';
        }
        $this->response($rs,200,$msg);
    }
    public function get_create(){
        $user_id = $this->input->get('user_id');
        $model = $this->Group_model;
        $pn = $this->input->get('pn');
        $all_num      = $model->get_all_cgroup_num($user_id);              //总条
        $page_num     = 20;                                       //每页条数
        $pageCount =ceil($all_num/$page_num);                //总页数
        if ($pageCount == 0){
            $pageCount =1;
        }
        if($pn > $pageCount){
            $pn = $pageCount;
        }
        $pn         =empty($pn)?1:$pn;                    //当前页数
        $pn         =(int)$pn;                              //安全强制转换
        $limit_st     =($pn-1)*$page_num;                     //起始数
        $re =  $model->get_create($limit_st,$page_num,$user_id);
        $rs['groups']=$this->Common_model->judge_image_exist($re);
        $rs['pageCount']  = $pageCount;
        $rs['currentPage'] = $pn;
        $rs['num']=$all_num;
        $rs['user_name']=$this->User_model->get_user_infomation($user_id)['nickname'];
        if(empty($re)){
            $msg = '暂无星球';
        }else{
            $msg = '获取星球列表成功';
        }
        $this->response($rs,200,$msg);
    }
    public function get_joined(){
        $user_id = $this->input->get('user_id');
        $model = $this->Group_model;
        $pn = $this->input->get('pn');
        $all_num      = $model->get_all_jgroup_num($user_id);              //总条
        $page_num     = 20;                                       //每页条数
        $pageCount =ceil($all_num/$page_num);                //总页数
        if ($pageCount == 0){
            $pageCount =1;
        }
        if($pn > $pageCount){
            $pn = $pageCount;
        }
        $pn         =empty($pn)?1:$pn;                    //当前页数
        $pn         =(int)$pn;                              //安全强制转换
        $limit_st     =($pn-1)*$page_num;                     //起始数
        $re =  $model->get_joined($limit_st,$page_num,$user_id);
        $rs['groups']=$this->Common_model->judge_image_exist($re);
        $rs['pageCount']  = $pageCount;
        $rs['currentPage'] = $pn;
        $rs['num']=$all_num;
        $rs['user_name']=$this->User_model->get_user_infomation($user_id)['nickname'];
        if(empty($re)){
            $msg = '暂无星球';
        }else{
            $msg = '获取星球列表成功';
        }
        $this->response($rs,200,$msg);
    }
    public function private_group(){
        $data = array(
            'user_id' =>$this->input->get('user_id'),
            'group_id' =>$this->input->get('group_id'),
            'p_text'    =>$this->input->get('p_text')
        );
        $model = $this->Group_model;
        $user_id=$this->User_model->get_group_infomation($data['group_id'])['user_base_id'];
        $re = $model->private_group($data,$user_id);
        if($re) {
            $rs['code'] = 1;
            $msg = '申请成功！请等待创建者审核！';
        }else {
            $rs['code'] = 0;
            $msg = '申请失败！';
        }
        /*
         * 调用前端接口  待测试
        $re=$this->Common_model->judgeUserOnline($user_id);

        if(empty($re)){
            $rs['code']=2;
        }
        */
        $this->response($rs,200,$msg);
    }
    public function user_manage(){
        $data = array(
            'user_id' =>$this->input->get('user_id'),
            'group_id' =>$this->input->get('group_id')
        );
        $user_id=$this->User_model->get_group_infomation($data['group_id'])['user_base_id'];
        if($user_id == $data['user_id']){
            $model = $this->Group_model;
            $rs = $model->user_manage($data);
            foreach($rs as $keys => $value){
                $profile_picture = $this->User_model->get_user_infomation($value['user_base_id'])['profile_picture'];
                if(empty($profile_picture)){
                    $profile_picture = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
                }
                $rs[$keys] = array(
                    'user_id'           =>$value['user_base_id'],
                    'user_name'         =>$this->User_model->get_user_infomation($value['user_base_id'])['nickname'],
                    'profile_picture'  =>$profile_picture,
                );
            }
            if($rs) {
                $re['group_id'] = $data['group_id'];
                $re['users'] = $rs;
                $re['code'] = 1;
                $msg = '显示成功！';
            }else {
                $re['code'] = 0;
                $msg = '该星球没有其他成员！';
            }
        }else{
            $re['code'] = 0;
            $msg = '您不是星球创建者，没有权限！';
        }
        $this->response($re,200,$msg);
    }
    public function delete_group_member(){
        $data = array(
            'user_id' =>$this->input->get('user_id'),
            'group_id' =>$this->input->get('group_id'),
            'member_id' =>$this->input->get('member_id')
        );
        $user_id=$this->User_model->get_group_infomation($data['group_id'])['user_base_id'];
        if($user_id == $data['user_id']){
            $model = $this->Group_model;
            $rs = $model->delete_group_member($data);
            if($rs) {
                $re['code'] = 1;
                $msg = '操作成功！并通知被删除的成员';
                $model->dgm_message($data);
            }else {
                $re['code'] = 0;
                $msg = '操作失败！';
            }
        }else{
            $re['code'] = 0;
            $msg = '您不是星球创建者，没有权限！';
        }
        $this->response($re,200,$msg);
    }
    public function search(){
        $data = array(
            'text'=>$this->input->get('text'),
            'gnum'=>$this->input->get('gnum'),
            'pnum'=>$this->input->get('pnum'),
            'gn'=>$this->input->get('gn'),
            'pn'=>$this->input->get('pn')
        );
        /*
        if(empty($data['gn'])||empty($data['gnum'])){
            $rs=$this->search_posts($data['text'],$data['pnum'],$data['pn']);
            $rs['group']=array();
        }elseif(empty($data['pn'])||empty($data['pnum'])){
            $rs=$this->search_group($data['text'],$data['gnum'],$data['gn']);
            $rs['posts']=array();
        }elseif(empty($data['gn'])&&empty($data['gnum'])&&empty($data['pn'])&&empty($data['pnum'])){
            $rs['group']=array();
            $rs['posts']=array();
        }else{
            $group=$this->search_group($data['text'],$data['gnum'],$data['gn']);
            $posts=$this->search_posts($data['text'],$data['pnum'],$data['pn']);
            $rs=array_merge($group,$posts);
        }
        */
        //$re = $this->delete_html_posts($rs);
        //$re = ($rs['posts']);
        $group=array();
        $posts=array();
        if(!empty($data['gnum'])){
            $group = $this->search_group($data['text'],$data['gnum'],$data['gn']);
        }
        if(!empty($data['pnum'])){
            $posts = $this->search_posts($data['text'],$data['pnum'],$data['pn']);
        }
        $rs=array_merge($group,$posts);
        $this->response($rs);
        //$a['b'] = array();
        //var_dump($rs);
    }

    private function search_posts($text,$pnum,$pn){
        $model=$this->Post_model;
        $page_num=$pnum;
        $all_num=$model->search_posts_num($text);
        $page_all_num =ceil($all_num/$page_num);                //总页数
        if ($page_all_num == 0){
            $page_all_num =1;
        }
        if($pn > $page_all_num){
            $pn = $page_all_num;
        }
        $re['posts']=$model->search_posts($text,$pnum,$pn);
        if(!empty($re['posts'])){
            $re['posts_page']=$page_all_num;
            $re['p_currentPage']=(int)$pn;
        }else{
            $re=array();
        }
        return $re;
    }
    private function search_group($text,$gnum,$gn){
        $model=$this->Group_model;
        $domain =$this->Common_model;
        $page_num=$gnum;
        $all_num=$model->search_group_num($text);
        $page_all_num =ceil($all_num/$page_num);                //总页数
        if ($page_all_num == 0){
            $page_all_num =1;
        }
        if($gn > $page_all_num){
            $gn = $page_all_num;
        }
        $rs=$model->search_group($text,$gnum,$gn);
        $re['group']=$domain->judge_image_exist($rs);
        if(!empty($re['group'])){
            $re['group_page']=$page_all_num;
            $re['g_currentPage']=(int)$gn;
        }else{
            $re=array();
        }
        return $re;
    }
    public function delete_html_posts($data){
        $rs = $data;
        if(!empty($rs)){
            for ($i=0; $i<count($rs['posts']); $i++) {
                $rs['posts'][$i]['text'] = strip_tags($rs['posts'][$i]['text']);
            }
        }
        return $rs;
    }
    public function test(){
        $data = array(
            'text'=>$this->input->get('text'),
            'gnum'=>$this->input->get('gnum'),
            'pnum'=>$this->input->get('pnum'),
            'gn'=>$this->input->get('gn'),
            'pn'=>$this->input->get('pn')
        );
        $rs = $this->search_group($data['text'],$data['gnum'],$data['gn']);
        $this->response($rs);

    }




}