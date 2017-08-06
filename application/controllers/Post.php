<?php



class Post extends REST_Controller
{
    /**
     * 构造函数，提前运行
     * Post constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        // $this->load->model('User_model');
        // $this->load->model('Group_model');
        $this->load->model('Common_model');
        $this->load->helper(array('form', 'url','url_helper'));
        $this->load->library(array('form_validation','jwt'));
        $this->form_validation->set_message('required', '{field}必填.');
        $this->form_validation->set_message('min_length', '{field}长度不小于{param}.');
        $this->form_validation->set_message('max_length', '{field}长度不大于{param}.');
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
            'latest'    => $this->get('latest')=='false'?FALSE:TRUE,
            'name'      => $this->get('name')
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

        date_default_timezone_set('UTC');
        foreach($re['data'] as $key=>$value){
            $re['data'][$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'content' => $value['content'],
                // 'lock'    => $value['lock'],
                'create_time'=>date('Y-m-d\TH:i:s\Z',$value['create_time']),
                'approved' => $value['approved']?TRUE:FALSE,
                'approved_num' => $value['approved_num'],
                'collected' => $value['collected']?TRUE:FALSE,
                'collected_num'     => $value['collected_num'],
                'replied'   => $value['replied']?TRUE:FALSE,
                'replied_num'   => $value['replied_num'],
                'image_url'      => $value['image'],
                'author' => [
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
        if($data['name']){
            $field = "&name={$data['name']}";
        }else{
            $field = '';
        }
        $re['paging'] = [
            'first'=> "{$host}/posts?limit={$limit}&offset=0{$field}",
            'previous'=>"{$host}/posts?limit={$limit}&offset={$lasto}{$field}",
            'next'=> "{$host}/posts?limit={$limit}&offset={$nexto}{$field}",
            'final'=> "{$host}/posts?limit={$limit}&offset={$finallyo}{$field}"
        ];

        $this->response($re);
    }

    /**
     * 点赞帖子
     * @param $post_id
     */
    public function approve_post($post_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'post_id'=>$post_id,
            'floor'=>$this->post('floor')?:1
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('approve_post') === FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断帖子回复是否存在
        if($this->db->get_where(
            'post_comment AS pc',
            "pc.post_base_id = {$data['post_id']} and pc.floor = {$data['floor']} and pc.delete = 0"
        )->row_array()){
            $this->response(['error'=>'楼层数不存在'],422);
        }

        //获取帖子点赞状态，并点赞，取消点赞
        $rs=$this->Post_model->get_approve_post($data);
        if($rs){
            $this->Post_model->update_approve_post($data)?
                $this->response(['success'=>'(取消)点赞成功'],200):
                $this->response(['error'=>'操作失败'],400);
        }else{
            $this->Post_model->add_approve_post($data)?
                $this->response(['success'=>'点赞成功'],200):
                $this->response(['error'=>'操作失败'],400);
        }

    }

    /**
     * 每个星球页面帖子显示
     * @param $group_id
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
        if(empty($re['data'])){
            $this->response($re,200);
        }
        $re = $this->Post_model->get_image_url($re);              //解析帖子内容，获得帖子中包含的图片
        $re = $this->Post_model->delete_image_gif($re);           //删除帖子中gif格式的图片
        $re = $this->Post_model->post_image_limit($re);           //展示图片限制，目前是显示三张
        $re = $this->Post_model->delete_html_posts($re);          //从帖子内容去除 HTML 和 PHP 标记
        $re = $this->Post_model->post_text_limit($re);            //帖子内容长度显示，目前是300字符

        date_default_timezone_set('UTC');
        foreach($re['data'] as $key=>$value){
            $re['data'][$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'content'  => $value['content'],
//                    'lock'    => $value['lock'],
                'digest'  => $value['digest']?TRUE:FALSE,
                'sticky'  => $value['sticky']?TRUE:FALSE,
                'create_time'=>date('Y-m-d\TH:i:s\Z',$value['create_time']),
                'approved'      => $value['approved']?TRUE:FALSE,
                'approved_num'  =>$value['approved_num'],
                'collected'     => $value['collected']?TRUE:FALSE,
                'collected_num' => $value['collected_num'],
                'replied'       => $value['replied']?TRUE:FALSE,
                'replied_num'   => $value['replied_num'],
                'image_url'         => $value['image'],
                'author' => [
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
     * @param $post_id
     */
    public function content_get($post_id){
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
            'post_id' =>$post_id,
            'user_id' =>$user_id?:0,
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('get_post_base') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录
        $post_info = $this->Post_model->get_post_information1($data['post_id']);
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        //获取帖子详情
        $post_info = $this->Post_model->get_post_base($data);
//        $this->response($post_info);

        $post_info['p_delete']?
            $this->response(['error'=>'帖子已被删除'],410):
            FALSE;

        $post_info['g_delete']?
            $this->response(['error'=>'帖子所属星球已关闭，不可查看！'],410):
            FALSE;

        if($post_info['g_private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$user_id);
            if(!$member&&$user_id!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可查看帖子'],403);
            }
        }
        preg_match_all("(http://[-a-zA-Z0-9@:%_\+.~#?&//=]+[.jpg.gif.png])",$post_info['content'],$post_info['image_url']);
        date_default_timezone_set('UTC');
        $array = [
            'id'=>$post_info['id'],
            'title'=>$post_info['title'],
            'content'=>$post_info['content'],
            'create_time'=>date('Y-m-d\TH:i:s\Z',$post_info['create_time']),
            'sticky'=>$post_info['sticky']?TRUE:FALSE,
            'lock'=>$post_info['lock']?TRUE:FALSE,
            'approved'=>$post_info['approved']?TRUE:FALSE,
            'approved_num'=>$post_info['approved_num'],
            'collected'=>$post_info['collected']?TRUE:FALSE,
            'collected_num'=>$post_info['collected_num'],
            'image_url'=>$post_info['image_url'],
            'group'=>[
                'id' =>$post_info['group_base_id'],
            ],
            'author'=>[
                'id'=>$post_info['user_base_id'],
                'name'=>$post_info['nickname'],
                'avatar_url'=>$post_info['profile_picture']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
            ]
        ];

        $this->response($array);
    }

    /**
     * 帖子的回复详情分页，方便多次调用
     * @param $data
     * @return array
     */
    private function comment_paging($data){
        //分页
        $offset = $data['offset'];
        $limit = $data['limit'];
        $page_count  = (ceil($data['all_num']/$limit)-1);                   //比总页数小 1
        $finallyo = $page_count * $limit;
        $lasto = ($offset-$limit)>0?($offset-$limit):0;
        $nexto = ($offset+$limit)<$finallyo?($offset+$limit):$finallyo;
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ?
                $_SERVER['HTTP_HOST'] :
                ''
            );

        return [
            'first'=> "{$host}/posts/{$data['post_id']}/comments?limit={$limit}&offset=0&floor={$data['reply_floor']}",
            'previous'=>"{$host}/posts/{$data['post_id']}/comments?limit={$limit}&offset={$lasto}&floor={$data['reply_floor']}",
            'next'=> "{$host}/posts/{$data['post_id']}/comments?limit={$limit}&offset={$nexto}&floor={$data['reply_floor']}",
            'final'=> "{$host}/posts/{$data['post_id']}/comments?limit={$limit}&offset={$finallyo}&floor={$data['reply_floor']}"
        ];
    }

    /**
     * 单个帖子的回复详情，不包括帖子内容
     * @param $post_id
     */
    public function comment_get($post_id){
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
            'post_id' =>$post_id,
            'user_id' =>$user_id?:0,
            'limit'     => $this->get('limit')?:20,                 //每页显示数
            'offset'    => $this->get('offset')?:0,                 //每页起始数
            'reply_floor'       =>1//本版本默认为1$this->get('floor')?:1            //楼层所在位置
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('post_comment') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录
        $post_info = $this->Post_model->get_post_information1($data['post_id']);
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        $post_info['p_delete']?
            $this->response(['error'=>'帖子已被删除'],410):
            FALSE;

        $post_info['g_delete']?
            $this->response(['error'=>'帖子所属星球已关闭，不可查看！'],410):
            FALSE;

        if($post_info['private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$user_id);
            if(!$member&&$user_id!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可查看帖子回复'],403);
            }
        }

        //获取帖子回复内容
        $rs = $this->Post_model->get_post_comment($data);
        $data['all_num'] = $this->Post_model->get_post_comment($data,TRUE);
        $re['reply_count'] = $data['all_num'];
        $re['paging'] = $this->comment_paging($data);                   //获取分页

//        foreach ($rs as $key=>$value){
//            $data['reply_floor'] = $value['floor'];
//            $rss = $this->Post_model->get_post_comment($data);
//            $data['all_num'] = $this->Post_model->get_post_comment($data,TRUE);
//            $rs[$key]['reply_count'] = $data['all_num'];
//            $rs[$key]['paging'] = $this->comment_paging($data);         //获取分页
//            $rs[$key]['reply'] = $rss;
//            foreach ($rss as $k => $v){
//                $data['reply_floor'] = $v['floor'];
//                $rsss = $this->Post_model->get_post_comment($data);
////                unset($rsss[$k]['floor']);
//                $rs[$key]['reply'][$k]['reply_count'] = $this->Post_model->get_post_comment($data,TRUE);
//                $rs[$key]['reply'][$k]['reply'] = $rsss;
//            }
//        }

        $re['reply'] = $rs;

        $this->response($re);
    }

    /**
     * 发表帖子
     * @param $group_id
     */
    public function create_post($group_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'user_id' =>$token->user_id,
            'group_id' =>$group_id,
            'title' =>$this->post('title'),
            'content' =>$this->post('content'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('posts') == FALSE)
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
            $member=$this->Common_model->judge_group_user($group_id,$data['user_id']);
            if(!$member&&$data['user_id']!=$group_info['user_base_id']){
                $this->response(['error'=>'您尚未加入私密星球，不可发帖！'],403);
            }
        }
        $rs = $this->Post_model->posts($data);
        if($rs){
            $this->response($rs);
        }else{
            $this->response(['error'=>'发表帖子失败'],400);
        }

    }

    /**
     * 回复帖子
     * @param $post_id
     */
    public function reply_post($post_id){

         //输出调试
//         $this->output->enable_profiler(TRUE);
//         print_r($this->db->queries);


        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'post_base_id' =>$post_id,
            'user_base_id' =>$token->user_id,
            'comment'  =>$this->post('comment'),
            'reply_floor'=>1        //V1.2.1版本仅支持回复楼主，reply_id为 @对象
        );

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('post_reply') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录
        $post_info = $this->Post_model->get_post_information1($data['post_base_id']);
        $data['reply_id'] = $this->post('reply_id');//V1.2.1版本仅支持回复楼主，reply_id为 @对象     ?:$post_info['user_base_id'];
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        $post_info['p_delete']?
            $this->response(['error'=>'帖子已被删除'],410):
            FALSE;

        $post_info['g_delete']?
            $this->response(['error'=>'帖子所属星球已关闭，不可回复！'],410):
            FALSE;

        if($post_info['private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$data['user_base_id']);
            if(!$member&&$data['user_base_id']!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可回复'],403);
            }
        }

        $post_info['lock']?
            $this->response(['error'=>'帖子被锁定，不可回复！'],403):
            FALSE;

        $data['floor'] = $data['reply_floor'];
        $data['floor'] != 1?
            $this->Post_model->get_reply($data)['user_id']!=$data['reply_id']?
                $this->response(['error'=>'回复失败,被回复楼层数和被回复人ID不关联'],400):
                FALSE:
            FALSE;
//        $this->response($this->Post_model->get_reply($data));
        //回复帖子
        $data['create_time'] = time();
        $comment = $this->Post_model->post_reply($data);
        if($comment == FALSE){
            $this->response(['error'=>'回复失败'],400);
        }
        $rs = $this->Post_model->get_reply($comment);
        date_default_timezone_set('UTC');
        $rs['create_time'] = date('Y-m-d\TH:i:s\Z',$rs['create_time']);
        $rs['approved'] = FALSE;$rs['approved_num'] = '0';
        $this->response($rs,200,TRUE);

        $this->Post_model->post_reply_message($data,$post_info);


    }

    /**
     * 编辑帖子
     * @param $post_id
     */
    public function edit_put($post_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'post_id' =>$post_id,
            'user_id' =>$token->user_id,
            'content'  =>$this->put('content'),
            'title'=>$this->put('title')
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('edit_post') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录
        $post_info = $this->Post_model->get_post_information1($data['post_id']);
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        $post_info['p_delete'] and $this->response(['error'=>'帖子已被删除'],410);

        $post_info['g_delete'] and $this->response(['error'=>'帖子所属星球已关闭，不可编辑帖子！'],410);

        if($post_info['private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$data['user_base_id']);
            if(!$member&&$data['user_base_id']!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可编辑帖子'],403);
            }
        }

        //判断编辑权限并编辑帖子
        $re = $this->judge_authority($token->user_id,$post_id);
        if($re['edit_right']===1){

            $this->Post_model->edit_post($data)?
                $this->response(['id'=>$post_id]):
                $this->response(['error'=>'编辑失败'],400);
        }else{
            $this->response(['error'=>'您没有权限编辑'],403);
        }
    }

    /**
     * 置顶帖子
     * @desc 帖子置顶
     * @param $post_id
     */
    public function sticky_put($post_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'post_id' =>$post_id,
            'user_id' =>$token->user_id
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录
        $post_info = $this->Post_model->get_post_information1($data['post_id']);
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        $post_info['p_delete']?
            $this->response(['error'=>'帖子已被删除'],410):
            FALSE;

        $post_info['g_delete']?
            $this->response(['error'=>'帖子所属星球已关闭，不可置顶帖子！'],410):
            FALSE;

        if($post_info['private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$data['user_id']);
            $admin = $this->Common_model->judge_admin($data['user_id']);
            if(!$member&&!$admin&&$data['user_base_id']!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可置顶帖子'],403);
            }
        }

        $re = $this->judge_authority($token->user_id,$post_id);
        if($re['sticky_right']===1){
            $this->Post_model->sticky_post($post_id)?
                $this->response('',204):
                $this->response(['error'=>'置顶失败'],400);
        }else{
            $this->response(['error'=>'您没有操作权限！'],403);
        }
    }

    /**
     * 删除帖子
     * @param $post_id
     */
    public function content_delete($post_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'post_id' =>$post_id,
            'user_id' =>$token->user_id
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录

        $post_info = $this->Post_model->get_post_information1($data['post_id']);
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        $post_info['p_delete']?
            $this->response(['error'=>'帖子已被删除'],410):
            FALSE;

        $post_info['g_delete']?
            $this->response(['error'=>'帖子所属星球已关闭，不可删除帖子！'],410):
            FALSE;

        if($post_info['private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$data['user_id']);
            $admin = $this->Common_model->judge_admin($data['user_id']);
            if(!$member&&!$admin&&$data['user_base_id']!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可删除帖子'],403);
            }
        }

        //判断删除权限并删除帖子
        $re = $this->judge_authority($token->user_id,$post_id);
        if($re['delete_right']===1){
            $this->Post_model->delete_post($re)?
                $this->response(['success'=>'删除成功'],200):
                $this->response(['error'=>'删除失败'],400);
        }else{
            $this->response(['error'=>'仅星球创建者和发帖者和管理员能删除帖子!'],403);
        }
    }

    /**
     * 删除帖子  存在相同方法，待删除 *2017/7/25 0025
     * @param $post_id
     */
    /**
     * public function delete_post(){
        $data=array(
            'user_id'=>$this->input->get('user_id'),
            'post_id'=>$this->input->get('post_id'),
        );
        $sqla=$this->Post_model->get_group_id($data['post_id']);
        $sqlb=$this->User_model->judge_create($data['user_id'],$sqla);
        $sqlc=$this->Post_model->judge_poster($data['user_id'],$data['post_id']);
        $sqld=$this->User_model->judge_admin($data['user_id']);
        if($sqlb||$sqlc||$sqld){
            $re=$this->Post_model->delete_post($data);
            $msg='成功删除帖子';
        }else{
            $re['code']=0;
            $msg='仅星球创建者和发帖者和管理员能删除帖子!';
        }
        $this->response($re,200,$msg);
    }*/

    /**
     * jwt解析函数
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
     * 判断权限方法  存在相同方法  后续会移除        *2017/7/25 0025
     * @param $jwt
     * @param $post_id
     * @return array
     */
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

    /**
     * 判断权限
     * @param $user_id
     * @param $post_id
     * @return array
     */
    private function judge_authority($user_id,$post_id){
        $creator= $this->Common_model->judge_group_creator($post_id,$user_id);
        $poster = $this->Common_model->judge_post_creator($user_id,$post_id);
        $admin = $this->Common_model->judge_admin($user_id);
        $rs = [
            'edit_right'=>0,
            'delete_right'=>0,
            'sticky_right'=>0,
            'lock_right'=>0,
            'user_id'=>$user_id,
            'post_id'=>$post_id,
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
     * @param $post_id
     */

    public function lock_put($post_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'post_id' =>$post_id,
            'user_id' =>$token->user_id
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录

        $post_info = $this->Post_model->get_post_information1($data['post_id']);
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        $post_info['p_delete']?
            $this->response(['error'=>'帖子已被删除'],410):
            FALSE;

        $post_info['g_delete']?
            $this->response(['error'=>'帖子所属星球已关闭，不可锁定帖子！'],410):
            FALSE;

        if($post_info['private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$data['user_id']);
            $admin = $this->Common_model->judge_admin($data['user_id']);
            if(!$member&&!$admin&&$data['user_base_id']!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可锁定帖子'],403);
            }
        }

        //判断锁定权限并锁定帖子
        $re = $this->judge_authority($token->user_id,$post_id);
        if($re['lock_right']===1){
            $this->Post_model->lock_post($post_id)?
                $this->response(['success'=>'锁定成功'],200):
                $this->response(['error'=>'锁定失败'],400);
        }else{
            $this->response(['error'=>'仅星球创建者和发帖者和管理员能锁定帖子!'],403);
        }
    }

    /**
     * 收藏帖子
     */
    public function collect_put($user_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'post_id'=>$this->put('post_id'),
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('collect_post') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        $post_exist = $this->Common_model->judge_post_exist($data['post_id']);
        $exist=$this->Common_model->ifexist_collect_post($data);
        if($exist){
            $this->Post_model->update_collect_post($data,$post_exist)?
                $this->response(['success'=>'(取消)收藏成功']):
                $this->response(['error'=>'(取消)收藏失败']);
        }else{
            if($post_exist&&$this->Post_model->collect_post($data)){
                $this->response(['success'=>'收藏成功']);
            }else{
                $this->response(['error'=>'收藏失败，帖子可能不存在']);
            }
        }
    }

    /**
     * 获取收藏的帖子
     */
    public function collect_get($user_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data = array(
            'user_id'   => $token->user_id,
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
        );
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('lists') == FALSE)
            $this->response(validation_errors(),422);

        //获得用户收藏帖子
        $re['data']=$this->Post_model->get_collect_post($data);
        if(empty($re['data'])){
            $this->response('',204);
        }
        $re = $this->Post_model->get_image_url($re);        //解析帖子内容，获得帖子中包含的图片
        $re = $this->Post_model->delete_image_gif($re);     //删除帖子中gif格式的图片
        $re = $this->Post_model->post_image_limit($re);     //展示图片限制，目前是显示三张
        $re = $this->Post_model->delete_html_posts($re);    //从帖子内容去除 HTML 和 PHP 标记
        $re = $this->Post_model->post_text_limit($re);      //帖子内容长度显示，目前是300字符
//        $this->response($re);
        date_default_timezone_set('UTC');
        foreach($re['data'] as $key=>$value){
            $re['data'][$key] = [
                'id' => $value['post_base_id'],
                'title' => $value['title'],
                'content' => $value['content'],
                'create_time'=>date('Y-m-d\TH:i:s\Z',$value['create_time']),
                'image_url'      => $value['image'],
                'delete'        => $value['delete'] or $value['g_delete'],
//                'author' => [
//                    'avatar_url' =>$value['profile_picture']?:'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
//                    'name'       =>$value['nickname'],
//                    'id'         =>$value['user_base_id'],
//                ],
                'group' => [
                    'id'  => $value['group_base_id'],
                    'name'    => $value['name'],
                ],
            ];
        }

        //分页
        $all_num = $this->Post_model->get_collect_post($data,TRUE);
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
            'first'=> "{$host}/users/{$user_id}/collections?limit={$limit}&offset=0",
            'previous'=>"{$host}/users/{$user_id}/collections?limit={$limit}&offset={$lasto}",
            'next'=> "{$host}/users/{$user_id}/collections?limit={$limit}&offset={$nexto}",
            'final'=> "{$host}/users/{$user_id}/collections?limit={$limit}&offset={$finallyo}"
        ];

        $this->response($re);
    }

    /**
     * 删除帖子回复
     * @param $post_id
     */
    public function comment_delete($post_id){
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data = array(
            'post_id' =>$post_id,
            'user_id' =>$token->user_id,
            'floor'  =>$this->delete('floor')
        );
        $this->form_validation->set_message('greater_than_equal_to', '{field}必须不小于{param}.');
        $this->form_validation->set_data($data);
        if ($this->form_validation->run('comment_delete') == FALSE)
            $this->response(['error'=>validation_errors()],422);

        //判断数据库中是否有记录

        $post_info = $this->Post_model->get_post_information1($data['post_id']);
        if(empty($post_info)){
            $this->response(['error'=>'帖子不存在！'],404);
        }

        $post_info['p_delete']?
            $this->response(['error'=>'帖子已被删除'],410):
            FALSE;

        $post_info['g_delete']?
            $this->response(['error'=>'帖子所属星球已关闭，不可删除回复！'],410):
            FALSE;

        if($post_info['private']){
            $member=$this->Common_model->judge_group_user($post_info['group_base_id'],$data['user_id']);
            $admin = $this->Common_model->judge_admin($data['user_id']);
            if(!$member&&!$admin&&$data['user_id']!=$post_info['user_base_id']){
                $this->response(['error'=>'私密星球，申请加入后方可删除回复'],403);
            }
        }

        $comment_info = $this->db->get_where('post_comment',[
            'post_base_id'      =>$data['post_id'],
            'floor'             =>$data['floor']
        ])->row_array();
        if(empty($comment_info)){
            $this->response(['error'=>'帖子回复不存在！'],404);
        }
        $comment_info['delete'] and $this->response(['error'=>'帖子回复已被删除'],410);

        $creator= $this->Common_model->judge_group_creator($post_info['group_base_id'],$data['user_id']);
        $floor_lord = $this->Post_model->judge_post_reply_user($data['user_id'],$data['post_id'],$data['floor']);
        $admin = $this->Common_model->judge_admin($data['user_id']);
//        dump($creator);dump($floor_lord);dump($admin);exit;
        if($creator||$floor_lord||$admin){
            $this->Post_model->delete_post_reply($data) and $this->response(['success'=>'成功删除帖子回复']);
        }else{
            $this->response(['error'=>'仅星球创建者和回帖者和管理员能删除帖子回复!'],403);
        }
    }

}
