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
    public function create(){
        $private=$this->input->get('private');
        $private=$this->Common_model->judgePrivate($private);
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'g_name'=>$this->input->get('g_name'),
            'g_image'=>$this->input->get('g_image'),
            'g_introduction'=>$this->input->get('g_introduction'),
            'private'=>$private,
        );
        $msg=null;
        $g_name=$this->input->get('g_name');
        $g_image=$this->input->get('g_image');
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
    public function join(){
        $data = array(
            'user_base_id' => $this->input->get('user_id'),
            'group_base_id'    => $this->input->get('group_id'),
            'authorization'=>'03',
        );
        $re=$this->Group_model->join($data);
        if($re){
            $this->Group_model->join_message($data);
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
     * 获取用户创建和加入的星球
     */
    public function get_user_group(){
        $user_id = $this->input->get('user_id');
        $data['user_id'] = $user_id;
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_create') == FALSE)
            $this->response(null,400,validation_errors());
        $model = $this->Group_model;
        $pn = $this->input->get('pn');
        $all_num      = $model->get_user_group_num($user_id);              //总条
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
        $re =  $model->get_user_group($limit_st,$page_num,$user_id);
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
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('private_group') == FALSE)
            $this->response(null,400,validation_errors());
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
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('private_group') == FALSE)
            $this->response(null,400,validation_errors());
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
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('delete_group_member') == FALSE)
            $this->response(null,400,validation_errors());
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
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('search') == FALSE)
            $this->response(null,400,validation_errors());
        $pn = empty($data['pn'])?1:$data['pn'];
        $gn = empty($data['gn'])?1:$data['gn'];
        $group=array('group'=>null);
        $posts=array('posts'=>null);
        if(!empty($data['gnum'])){
            $group = $this->search_group($data['text'],$data['gnum'],$gn);
        }
        if(!empty($data['pnum'])){
            $posts = $this->search_posts($data['text'],$data['pnum'],$pn);
        }
        $rs=array_merge($group,$posts);
        if(!empty($rs['posts'])){
        $rs = $this->Post_model->delete_html_posts($rs);
        }
        $this->response($rs);
    }

    /**
     * 退出星球接口
     * @desc 用户退出星球
     * @return int code 操作码，1表示退出成功，0表示退出失败
     * @return string msg 提示信息
     */
    public function quit(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'group_id'=>$this->input->get('group_id'),
        );
        $creator=$this->Group_model->judge_group_creator($data);
        if($creator){
            $msg='您是星球创建者，无法退出';
            $re['code']=0;
        }else{
            $this->Group_model->quit($data);
            $this->Group_model->quit_message($data);
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
            $re=array('posts'=>null);
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
            $re=array('group'=>null);
        }
        return $re;
    }

    /**
     * 判断用户是否加入该星球
     * @desc 判断用户是否加入该星球
     * @return int code 操作码，1表示已加入，0表示未加入
     * @return string msg 提示信息
     */
    public function g_status(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'g_id'=>$this->input->get('group_id'),
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
    public function get_group_info(){
        $group_id=$this->input->get('group_id');
        $user_id=$this->input->get('user_id');
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$user_id,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('private_group') == FALSE)
            $this->response(null,400,validation_errors());
        $group_exist=$this->Group_model->judge_group_exist($group_id);
        $creator=$this->Group_model->judge_group_creator($data);
        if($group_exist){
            $rs=$this->Group_model->get_group_infomation($group_id);
            if(empty($rs['g_image'])){
                $rs['g_image'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
            $re = [
                'group_id'=>$rs['id'],
                'g_name'=>$rs['name'],
                'g_introduction'=>$rs['g_introduction'],
                'g_image'=>$rs['g_image'],
                'private'=>$rs['private'],
            ];
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
    public function alter_group_info(){
        $group_id=$this->input->get('group_id');
        $user_id=$this->input->get('user_id');
        $g_introduction=$this->input->get('g_introduction');
        $g_image=$this->input->get('g_image');
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$user_id,
            'g_introduction'=>$g_introduction,
            'g_image'=>$g_image,
            'private'=>$this->input->get('private'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('private_group') == FALSE)
            $this->response(null,400,validation_errors());
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
            'user_id' =>$this->input->post('user_id'),
            'group_id' =>$this->input->post('group_id'),
            'p_title' =>$this->input->post('p_title'),
            'p_text' =>$this->input->post('p_text'),
        );
        //$this->form_validation->set_data($data);
        if ($this->form_validation->run('posts') == FALSE)
            $this->response(null,400,validation_errors());
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

/**
 *
 * 判断用户登陆状态-判断是否登录
 */
    public function check_status($user_id=null){
        //setcookie('user_name','lwy_test');
        $rs = array();
        $user_id = $this->input->get('user_id');
        
        $dat = $this->User_model->get_user_information($user_id);
        //print_r($dat);

        if(isset($_COOKIE['user_name'])&&$_COOKIE['user_name'] == $dat['nickname'])
        {
            $rs['code'] = 1;
            $rs['info'] = array(
                'user_id' => $dat['id'],
                'user_name' =>$dat['nickname']);
            $msg = "用户已登陆";
        }
        else
        {
            $code = 0;
            $rs['info'] = array(
                'user_id' => $dat['id'],
                'user_name' =>$dat['nickname']);
            $msg = "用户未登录";
        }
        $this->response($rs,200,$msg);
       
    }







}