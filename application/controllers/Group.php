<?php



class Group extends REST_Controller
{
    /**
     * Group constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Group_model');
        $this->load->model('Common_model');
        $this->load->model('User_model');
        $this->load->model('Post_model');
        $this->load->helper(array('form', 'url','url_helper'));
        $this->load->library(array('form_validation','jwt'));
        $this->form_validation->set_message('required', '{field}必填.');
        $this->form_validation->set_message('min_length', '{field}长度不小于{param}.');
        $this->form_validation->set_message('is_natural_no_zero', '{field}不是正整数.');
        $this->form_validation->set_message('is_natural', '{field}不是自然数.');
        $this->form_validation->set_message('max_length', '{field}长度不大于{param}.');
    }

    /**
     * 解析jwt，获得用户id
     * @param $jwt
     * @return mixed
     */
    private function parsing_token($jwt)
    {
        try{
            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
            return $token;
        }
        catch(InvalidArgumentException $e)
        {
            return $this->response(['error'=>'身份信息已失效，请重新获取'],401);
        }
        catch(UnexpectedValueException $e)
        {
            return $this->response(['error'=>'身份信息已失效，请重新获取'],401);
        }

    }

    /**
     * 星球创建接口-用于创建星球
     */
    public function create_post(){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'g_name'=>$this->post('name'),
            'g_image'=>$this->post('image_url')?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
            'g_introduction'=>$this->post('introduction'),
            'private'=>$this->post('private')?1:0
        );
        $this->form_validation->set_message('regex_match', '{field}只能为中文、英文、数字或者下划线组合，但不得超过20个字符');
        $this->form_validation->set_message('valid_url','{field}url地址不合法');
        $this->form_validation->set_message('is_string','{field}的数据类型必须是字符型');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('create_group') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断星球名是否重复
        $repeat = $this->Group_model->gname_exist($data['g_name']);
        if($repeat){
            $this->response(['error'=>'该星球名称已存在'],422);
        }else{
            $create=$this->Group_model->create($data);
            $this->response($create);
        }
    }

    /**
     * 加入星球接口
     * @param $group_id
     */
    public function join_post($group_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'group_id'    => $group_id,
            'user_id' =>$token->user_id
        );
//        $this->form_validation->set_message('is_int','{field}的数据类型必须是整型');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('join_group') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断用户是否为星球成员
        $this->Common_model->judge_group_user($group_id,$token->user_id) and $this->response(['error'=>'您已在该星球'],400);
        //判断用户是否是星球创建者
        $this->Common_model->judge_group_creator($group_id,$token->user_id) and $this->response(['error'=>'您已在该星球'],400);

        $field = array(
            'group_base_id' => $data['group_id'],
            'user_base_id'  => $data['user_id'],
            'authorization' => "03",
        );
        $this->Group_model->join_group($field);
        $this->Group_model->join_message($field);
        $this->response(['success'=>'加入成功！并通知星球创建者']);

    }

    /**
     * 退出星球接口
     * @param $group_id
     */
    public function quit_delete($group_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'group_id'=>$group_id,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('join_group') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        $creator=$this->Common_model->judge_group_creator($group_id,$data['user_id']);
        $member = $this->Common_model->judge_group_user($group_id,$token->user_id);
        if($creator){
            $this->response(['error'=>'您是星球创建者，无法退出'],400);
        }
        if($member){
            $this->Group_model->quit($data);
            $this->Group_model->quit_message($data);
            $this->response(['success'=>'退出成功！并通知星球创建者']);
        }else{
            $this->response(['error'=>'您不是星球成员，无需退出'],400);
        }
    }

    /**
     * 判断用户是否加入该星球
     * @param $group_id
     * @param $m_id
     */
    public function status_get($group_id,$m_id){
        $data=array(
            'user_id'=>$m_id,
            'group_id'=>$group_id,
        );

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('private_group') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        $this->response([
            'success'=>$this->Common_model->judge_group_user($group_id,$m_id)
        ]);
    }

    /**
     * 星球列表
     */
    public function lists_get(){
        //校验权限

        if(!empty($this->get('user_id'))){
            $jwt = $this->input->get_request_header('Access-Token', TRUE);
            $token = $this->parsing_token($jwt);
            $user_id = $token->user_id;
            if($user_id!=$this->get('user_id')){
                $this->response(['error'=>'您没有权限查看其他人的星球'],403);
            }
        }
        //输入参数验证
        $data=array(
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('lists') === FALSE)
            $this->response(['error'=>validation_errors()],422);


        //获取星球详情
        $offset = $data['offset'];
        $limit = $data['limit'];
        $name = $this->get('name');
        $user_id = $this->get('user_id');
        $group = $this->Group_model->lists($offset,$limit,$name,$user_id);
        if(empty($group)){
            $this->response('',204);
        }
        $all_num = $this->Group_model->get_group_num($name,$user_id);         //消息总数
        $rs['data']=$this->Common_model->judge_image_exist($group);

        //分页
        //星球可能被删除，所以这里不是数据库所有数据返回
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
        if($name){
            $field = "&name={$name}";
        }elseif($user_id){
            $field = "&user_id={$user_id}";
        }else{
            $field = '';
        }
        $rs['paging'] = [
            'first'=> "{$host}/groups?limit={$limit}&offset=0{$field}",
            'previous'=>"{$host}/groups?limit={$limit}&offset={$lasto}{$field}",
            'next'=> "{$host}/groups?limit={$limit}&offset={$nexto}{$field}",
            'final'=> "{$host}/groups?limit={$limit}&offset={$finallyo}{$field}"
        ];

        $this->response($rs);
    }

    /**
     * 获取星球详情
     * @param $group_id
     */
    public function group_info_get($group_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        if(empty($jwt)){
            $user_id = NULL;
        }else{
            $token = $this->parsing_token($jwt);
            $user_id = $token->user_id;
        }

        //输入参数校验
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$user_id,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_group_post') == FALSE)
            $this->response(null,400,validation_errors());

        //星球详情
        if($this->Common_model->judge_group_exist($group_id)){
            $rs=$this->Group_model->get_group_infomation($group_id);
            if(empty($user_id)){
                $identity = 'not_applied';
            }else{
                if($this->Common_model->judge_group_creator($group_id,$user_id)){
                    $identity = 'creator';
                }elseif ($this->Common_model->judge_group_user($group_id,$user_id)){
                    $identity = 'member';
                }elseif ($this->Common_model->judge_user_application($user_id,$group_id)){
                    $identity = 'is_applied';
                }else{
                    $identity = 'not_applied';
                }
            }
            $re = [
                'id'               =>$rs['id'],
                'name'             =>$rs['name'],
                'introduction'     =>$rs['g_introduction'],
                'image_url'        =>$rs['g_image']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
                'private'          =>$rs['private']?TRUE:FALSE,
                'creator'          =>[
                    'id'        =>$rs['user_base_id'],
                    'name'      =>$this->User_model->get_user_information($rs['user_base_id'])['nickname'],
                ],
                'post_num'         =>$this->Group_model->get_group_post_num($group_id),
                'member_num'       =>$this->Group_model->get_group_user_num($group_id),
                'identity'         =>$identity
            ];
            $this->response($re);
        }else{
            $this->response(['error'=>'星球不存在'],404);
        }
    }

    /**
     * 修改星球接口
     * @param $group_id
     */
    public function group_info_put($group_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data=array(
            'group_id'=>$group_id,
            'user_id'=>$token->user_id,
            'g_introduction'=>$this->put('introduction'),
            'g_image'=>$this->put('image_url'),
            'private'=>$this->put('private')?1:0,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_group_post') == FALSE)
            $this->response(null,400,validation_errors());

        //修改星球信息
        if($this->Common_model->judge_group_exist($group_id)){
            if($this->Common_model->judge_group_creator($group_id,$data['user_id'])){
                if($this->Group_model->alter_group_info($data)){
                    $this->response(NULL,204);
                }else{
                    $this->response(['error'=>'修改失败'],400);
                }
            }else{
                $this->response(['error'=>'您不是创建者没有权限修改'],403);
            }
        }else{
            $this->response(['error'=>'星球不存在'],404);
        }
    }

    /**
     * 申请加入私密星球
     * @param $group_id
     */
    public function private_group_post($group_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'user_id' =>$token->user_id,
            'group_id' =>$group_id,
            'text'    =>$this->post('text')
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_group_post') == FALSE)
            $this->response(null,400,validation_errors());

        //申请加入私密星球
        if($this->Common_model->judge_group_private($group_id)){
            if($this->Group_model->private_group(
                $data,$this->Group_model->get_group_infomation($data['group_id'])['user_base_id']
            )){
                /**
                 * 调用前端接口  待测试
                 *  $this->Common_model->judgeUserOnline($user_id);
                 */
                $this->response(NULL,204);
            }else{
                $this->response(['error'=>'申请失败'],400);
            }
        }else{
            $this->response(['error'=>'该星球不是私密星球，无需申请'],400);
        }
    }

    /**
     * 获取星球成员
     * @param $group_id
     */
    public function member_get($group_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数验证
        $data = array(
            'user_id' =>$token->user_id,
            'group_id' =>$group_id,
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_group_post') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //获取星球成员
        $creator_id=$this->Group_model->get_group_infomation($data['group_id'])['user_base_id'];
        if($creator_id == $data['user_id']){
            $rs = $this->Group_model->group_member($data);
            foreach($rs as $keys => $value){
                $re['data'][$keys] = array(
                    'id'           =>$value['user_base_id'],
                    'name'         =>$this->User_model->get_user_information($value['user_base_id'])['nickname'],
                    'avatar_url'  =>$this->User_model->get_user_information($value['user_base_id'])['profile_picture']
                        ?:
                        'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
                );
            }

            //分页
            $limit = $data['limit'];
            $offset = $data['offset'];
            $all_num = $this->Group_model->group_member($data,TRUE);;   //消息总数
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
                'first'=> "{$host}/groups/{$group_id}/members?limit={$limit}&offset=0",
                'previous'=>"{$host}/groups/{$group_id}/members?limit={$limit}&offset={$lasto}",
                'next'=> "{$host}/groups/{$group_id}/members?limit={$limit}&offset={$nexto}",
                'final'=> "{$host}/groups/{$group_id}/members?limit={$limit}&offset={$finallyo}"
            ];

            if($re['data']) {
                $this->response($re);
            }else {
                $this->response(NULL,204);
            }
        }else{
            $this->response(['error'=>'您不是星球创建者，没有权限！'],403);
        }
    }

    /**
     * 删除星球成员
     * @param $group_id
     * @param $m_id
     */
    public function member_delete($group_id,$m_id){
        //校验权限
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数验证
        $data = array(
            'user_id' =>$token->user_id,
            'group_id' =>$group_id,
            'member_id' =>$m_id
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('delete_group_member') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        $creator_id=$this->Group_model->get_group_infomation($data['group_id'])['user_base_id'];
        if($creator_id == $data['user_id']){
            $model = $this->Group_model;
            $rs = $model->delete_group_member($data);
            if($rs) {
                $model->dgm_message($data);
                $this->response(NULL,204);
            }else {
                $this->Common_model->judge_group_creator($group_id,$data['user_id'])?
                    $this->response(['error'=>'您是星球主人，无法删除自己'],400):
                    $this->response(['error'=>'成员不在该星球，无需删除'],400);
            }
        }else{
            $this->response(['error'=>'您不是星球创建者，没有权限！'],403);

        }
    }

    /**
     * 搜索接口，搜索星球或者帖子
     */
//    public function search(){
//        $data = array(
//            'text'=>$this->get('text'),
//            'gnum'=>$this->get('gnum'),
//            'pnum'=>$this->get('pnum'),
//            'gn'=>$this->get('gn'),
//            'pn'=>$this->get('pn')
//        );
//        $this->form_validation->set_data($data);
//        if ($this->form_validation->run('search') == FALSE)
//            $this->response(null,400,validation_errors());
//        $pn = empty($data['pn'])?1:$data['pn'];
//        $gn = empty($data['gn'])?1:$data['gn'];
//        $group=array('group'=>null);
//        $posts=array('posts'=>null);
//        if(!empty($data['gnum'])){
//            $group = $this->search_group($data['text'],$data['gnum'],$gn);
//        }
//        if(!empty($data['pnum'])){
//            $posts = $this->search_posts($data['text'],$data['pnum'],$pn);
//        }
//        $rs=array_merge($group,$posts);
//        if(!empty($rs['posts'])){
//        $rs = $this->Post_model->delete_html_posts($rs);
//        }
//        $this->response($rs);
//    }

//    private function search_posts($text,$pnum,$pn){
//        $model=$this->Post_model;
//        $page_num=$pnum;
//        $all_num=$model->search_posts_num($text);
//        $page_all_num =ceil($all_num/$page_num);                //总页数
//        if ($page_all_num == 0){
//            $page_all_num =1;
//        }
//        if($pn > $page_all_num){
//            $pn = $page_all_num;
//        }
//        $re['posts']=$model->search_posts($text,$pnum,$pn);
//        if(!empty($re['posts'])){
//            foreach($re['posts'] as $key=>$value){
//                $profile_picture = $this->User_model->get_user_information($value['user_id'])['profile_picture'];
//                if(empty($profile_picture)){
//                    $profile_picture = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
//                }
//                $re['posts'][$key]['profile_picture'] = $profile_picture;
//            }
//            $re['posts_page']=$page_all_num;
//            $re['p_current_page']=(int)$pn;
//        }else{
//            $re=array('posts'=>null);
//        }
//        return $re;
//    }




}