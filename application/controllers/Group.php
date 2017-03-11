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
     * 星球创建接口
     * @desc 用于创建星球
     * @return int code 操作码，1表示创建成功，0表示创建失败
     * @return object info 星球信息对象
     * @return int info.group_base_id 星球ID
     * @return string info.user_base_id 创建者ID
     * @return string info.authorization 权限，01表示创建者
     * @return string info.name 星球名称
     * @return string msg 提示信息
     */
    public function create($user_id,$g_name,$g_image=null,$g_introduction=null,$private=null){
        $private=$this->Common_model->judgePrivate($private);
        $data=array(
            'user_id'=>$user_id,
            'g_name'=>$g_name,
            'g_image'=>$g_image,
            'g_introduction'=>$g_introduction,
            'private'=>$private,
        );
        $msg=null;
        $check_group_name=$this->check_group_name($g_name);
        if($check_group_name['code']){
            if(empty($g_image)){
                $data['g_image']='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
            $create=$this->Group_model->create($data);
            if(!empty($create)){
                $re['code']=1;
                $re['info']=$create;
                $msg='创建成功！';
            }else{
                $re['code']=0;
                $msg='创建失败';
            }
        }else{
            $msg=$check_group_name['msg'];
            $re['code']=$check_group_name['code'];
        }

        $this->response($re,200,$msg);
    }

    /**
     * 加入星球接口
     * @desc 用户加入星球
     * @return int code 操作码，1表示加入成功，0表示加入失败
     * @return object info 星球信息对象
     * @return int info.group_base_id 加入星球ID
     * @return string info.user_base_id 加入者ID
     * @return string info.authorization 权限，03表示会员
     * @return string msg 提示信息
     */
    public function join($user_id,$g_id){
        $data = array(
            'user_base_id' => $user_id,
            'group_base_id'    => $g_id,
            'authorization'=>'03',
        );
        $re=$this->Group_model->join($data);
        if($re){
            $msg='加入成功！并通知星球创建者';
            $rs['code']=1;
        }else{
            $msg='加入失败';
            $rs['code']=0;
        }
        $this->response($re,200,$msg);
    }
    /**
     * 星球列表
     */
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
        $rs['groups']=$this->Common_model->judge_image_exist($re);
        $rs['page_count']  = $pageCount;
        $rs['current_page'] = $pn;
        if(empty($re)){
            $msg = '暂无星球';
        }else{
            $msg = '获取星球列表成功';
        }
        $this->response($rs,200,$msg);
    }
    /**
     * 获取用户创建的星球
     */
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
        $rs['page_count']  = $pageCount;
        $rs['current_page'] = $pn;
        $rs['num']=$all_num;
        $rs['user_name']=$this->User_model->get_user_information($user_id)['nickname'];
        if(empty($re)){
            $msg = '暂无星球';
        }else{
            $msg = '获取星球列表成功';
        }
        $this->response($rs,200,$msg);
    }
    /**
     * 获取用户加入的星球
     */
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
        $rs['page_count']  = $pageCount;
        $rs['current_page'] = $pn;
        $rs['num']=$all_num;
        $rs['user_name']=$this->User_model->get_user_information($user_id)['nickname'];
        if(empty($re)){
            $msg = '暂无星球';
        }else{
            $msg = '获取星球列表成功';
        }
        $this->response($rs,200,$msg);
    }
    /**
     * 申请加入私密星球
     */
    public function private_group(){
        $data = array(
            'user_id' =>$this->input->get('user_id'),
            'group_id' =>$this->input->get('group_id'),
            'text'    =>$this->input->get('text')
        );
        $model = $this->Group_model;
        $user_id=$model->get_group_infomation($data['group_id'])['user_base_id'];
        $re = $model->private_group($data,$user_id);
        if($re) {
            $rs['code'] = 1;
            $msg = '申请成功！请等待创建者审核！';
        }else {
            $rs['code'] = 0;
            $msg = '申请失败！';
        }
        /**
         * 调用前端接口  待测试
        $re=$this->Common_model->judgeUserOnline($user_id);

        if(empty($re)){
            $rs['code']=2;
        }
        */
        $this->response($rs,200,$msg);
    }
    /**
     * 星球成员管理
     */
    public function user_manage(){
        $data = array(
            'user_id' =>$this->input->get('user_id'),
            'group_id' =>$this->input->get('group_id')
        );
        $user_id=$this->Group_model->get_group_infomation($data['group_id'])['user_base_id'];
        if($user_id == $data['user_id']){
            $model = $this->Group_model;
            $rs = $model->user_manage($data);
            foreach($rs as $keys => $value){
                $profile_picture = $this->User_model->get_user_information($value['user_base_id'])['profile_picture'];
                if(empty($profile_picture)){
                    $profile_picture = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
                }
                $rs[$keys] = array(
                    'user_id'           =>$value['user_base_id'],
                    'user_name'         =>$this->User_model->get_user_information($value['user_base_id'])['nickname'],
                    'profile_picture'  =>$profile_picture,
                );
            }
            if($rs) {
                $re['group_id'] = $data['group_id'];
                $re['users'] = $rs;
                $re['code'] = 1;
                $msg = '显示成功！';
            }else {
                $re['group_id'] = $data['group_id'];
                $re['users'] = NULL;
                $re['code'] = 0;
                $msg = '该星球没有其他成员！';
            }
        }else{
            $re['group_id'] = $data['group_id'];
            $re['code'] = 0;
            $msg = '您不是星球创建者，没有权限！';
        }
        $this->response($re,200,$msg);
    }
    /**
     * 删除星球成员
     */
    public function delete_group_member(){
        $data = array(
            'user_id' =>$this->input->get('user_id'),
            'group_id' =>$this->input->get('group_id'),
            'member_id' =>$this->input->get('member_id')
        );
        $user_id=$this->Group_model->get_group_infomation($data['group_id'])['user_base_id'];
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
    /**
     * 搜索接口，搜索星球或者帖子
     */
    public function search(){
        $data = array(
            'text'=>$this->input->get('text'),
            'gnum'=>$this->input->get('gnum'),
            'pnum'=>$this->input->get('pnum'),
            'gn'=>$this->input->get('gn'),
            'pn'=>$this->input->get('pn')
        );
        $pn = empty($data['pn'])?1:$data['pn'];
        $gn = empty($data['gn'])?1:$data['gn'];
        $group=array();
        $posts=array();
        if(!empty($data['gnum'])){
            $group = $this->search_group($data['text'],$data['gnum'],$gn);
        }
        if(!empty($data['pnum'])){
            $posts = $this->search_posts($data['text'],$data['pnum'],$pn);
        }
        $rs=array_merge($group,$posts);
        /**
         * 此处为临时调用方法，待合代码之后统一改为$this->Post_model->delete_html_posts($rs)方法
         */
        $rs = $this->Post_model->delete_html_posts_1($rs);
        $this->response($rs);
    }

    /**
     * 退出星球接口
     * @desc 用户退出星球
     * @return int code 操作码，1表示退出成功，0表示退出失败
     * @return string msg 提示信息
     */
    public function quit($user_id,$g_id){
        $data=array(
            'user_id'=>$user_id,
            'group_id'=>$g_id,
        );
        $creator=$this->Group_model->judge_group_creator($data);
        if($creator){
            $msg='您是星球创建者，无法退出';
            $re['code']=0;
        }else{
            $this->Group_model->quit($data);
            $re['code']=1;
            $msg='退出成功！并通知星球创建者';
        }
        $this->response($re,200,$msg);
    }
    /**
     * @param $text
     * @param $pnum
     * @param $pn
     * @return array
     * 搜索帖子
     */
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
            $re['p_current_page']=(int)$pn;
        }else{
            $re=array();
        }
        return $re;
    }
    /**
     * @param $text
     * @param $gnum
     * @param $gn
     * @return array
     * 搜索星球
     */
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
            $re['g_current_page']=(int)$gn;
        }else{
            $re=array();
        }
        return $re;
    }
    /**
     * @param $data
     * @return mixed
     * 删除帖子的html标签
     */
    private function delete_html_posts($data){
        $rs = $data;
        if(!empty($rs)){
            for ($i=0; $i<count($rs['posts']); $i++) {
                $rs['posts'][$i]['text'] = strip_tags($rs['posts'][$i]['text']);
            }
        }
        return $rs;
    }
    /**
     * 测试接口，待完成所有接口之后删除
     */
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



    /**
     * 判断用户是否加入该星球
     * @desc 判断用户是否加入该星球
     * @return int code 操作码，1表示已加入，0表示未加入
     * @return string msg 提示信息
     */
    public function g_status($user_id,$g_id){
        $data=array(
            'user_id'=>$user_id,
            'g_id'=>$g_id,
        );
        $re['code']=$this->Group_model->g_status($data);
        if($re['code']) {
            $msg = '已加入该星球';
        }else{
            $msg='未加入该星球';
        }
        $this->response($re,200,$msg);
    }


    /**
     *获取星球详情
     * @desc 获取星球详情接口
     * @return int groupID 星球id
     * @return string groupName 星球名称
     * @return string g_introduction 星球介绍
     * @return string g_image 星球图片链接
     * @return int creator 是否为创建者，1为创建者，0不是创建者
     */
    public function get_group_info($group_id,$user_id){
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$user_id,
        );
        $group_exist=$this->Group_model->judge_group_exist($group_id);
        $creator=$this->Group_model->judge_group_creator($data);
        if($group_exist){
            $re=$this->Group_model->get_group_info($group_id);
            if($creator){
                $re['creator']=1;
            }else{
                $re['creator']=0;
            }
        }else{
            $re=0;
        }
        $this->response($re,200,null);
    }

    /**
     *修改星球接口
     * @desc 修改星球详情
     * @return int data 0代表修改失败,1代表修改成功
     * @return string msg 提示错误信息
     */
    public function alter_group_info($group_id,$user_id,$g_introduction=null,$g_image=null){
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$user_id,
            'g_introduction'=>$g_introduction,
            'g_image'=>$g_image,
        );
        $group_exist=$this->Group_model->judge_group_exist($group_id);
        $creator=$this->Group_model->judge_group_creator($data);
        if($group_exist){
            if($creator){
                $this->Group_model->alter_group_info($data);
                $re['code']=1;
                $msg='修改成功';
            }else{
                $re['code']=0;
                $msg='不是创建者';
            }
        }else{
            $re['code']=0;
            $msg='星球不存在';
        }
        $this->response($re,200,$msg);
    }







    /*
     * 判断星球名称是否合法
     */
    public function check_group_name($g_name){
        $re=$this->Group_model->gname_exist($g_name);
        $rs['code']=0;
        if (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u', $g_name)) {
            $rs['msg'] = '小组名只能为中文、英文、数字或者下划线，但不得超过20字节！';
        }elseif (!empty($re)) {
            $rs['msg'] = '该星球已创建！';
        }else{
            $rs['code']=1;
        }
        return $rs;
    }
    /**
     * 发表帖子
     */
    public function posts(){
        $data = array(
            'user_id' =>$this->input->get('user_id'),
            'group_id' =>$this->input->get('group_id'),
            'p_title' =>$this->input->get('p_title'),
            'p_text' =>$this->input->get('p_text'),
        );
        $boola = $this->Common_model->check_group($data['user_id'],$data['group_id']);
        $boolb = $this->Common_model->judge_group_creator($data['group_id'],$data['user_id']);
        $rs['code'] = 0;
        if ($boola||$boolb) {
            $post_id = $this->Group_model->posts($data);
            $rs['post_id'] = $post_id;
            $rs['code'] = 1;
            $msg = '发表成功';
        }else{
            $msg = '未加入该星球';
        }
        $this->response($rs,200,$msg);
    }





}