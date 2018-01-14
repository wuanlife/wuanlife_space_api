<?php
/**
 * Created by PhpStorm.
 * User: tacer
 * Date: 2017/12/28
 * Time: 19:52
 */

class Articles extends REST_Controller
{
    public function __construct(string $config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('articles_model');
        $this->load->model('users_model');
        $this->load->model('admins_model');
        $this->load->library(array('form_validation','jwt'));
    }
    /**
     * 解析jwt，获得用户id（旧的拷贝过来的）
     * @param $jwt
     * @return mixed
     */
    private function parsing_token($jwt)
    {
        try{
            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
            return $token;
        }
        catch(Exception $e)
        {
            return $this->response(['error'=>'未登录，不能操作'],401);
        }
    }

    /**
     * 发表文章/发表评论
     * @param null $aid
     * @param null $type
     * @param null $floor
     */
    public function index_post($aid = null, $type = null, $floor = null): void
    {
        //校验权限
        $token = $this->input->get_request_header('Access-Token', TRUE);
        $user_info = $this->parsing_token($token);
        $userArr = $this->users_model->getUserInfo($user_info->user_id);

        //处理URL变量
        $aid_null = is_null($aid);
        $type_null = is_null($type);
        $floor_null = is_null($floor);
        $aid = intval($aid);
        $floor = intval($floor);

        if($aid_null && $type_null && $floor_null){      //发表文章
            /* 获取POST数据 */
            $title = trim($this->post('title'));
            $content = trim($this->post('content'));
            $content_txt = str_replace('&nbsp;','',strip_tags($content));
            $image_urls = $this->post('image_urls');
            $image_urls_arr = explode(',', $image_urls);

            //验证POST数据
            empty($title) and $this->response(['error'=>'文章标题不能为空'], 400);
            mb_strlen($title) > 60 and $this->response(['error'=>'标题不能超过60个字符'], 400);
            empty($content) and $this->response(['error'=>'文章正文不能为空'], 400);
            mb_strlen($content_txt) > 5000 and $this->response(['error'=>'文章正文不能超过5000个字符'], 400);
            count($image_urls_arr) > 3 and $this->response(['error'=>'至多三张预览图片'], 400);

            //组合数据
            $data = [
                'user_id'=>$userArr['id'],
                'user_name'=>$userArr['name'],
                'title'=>$title,
                'content'=>$content,
                'resume'=>substr($content_txt,0,90).'...',
                'image_urls_arr'=>$image_urls_arr
            ];

            $result['id'] = $this->articles_model->articleAdd($data);
            if($result['id'] > 0){
                $this->response($result, 200);
            }else{
                $this->response(['error'=>'创建失败'], 400);
            }
        }else if(!$aid_null && $type=='comments' && $floor_null){      //评论文章
            /* 获取POST数据 */
            $comment = trim($this->post('comment'));

            //验证POST数据
            empty($comment) and $this->response(['error'=>'回复内容不能为空'], 400);
            mb_strlen($comment) > 5000 and $this->response(['error'=>'回复内容不能超过5000个字符'], 400);
            //验证文章权限
            $status = $this->articles_model->get_status_post($aid);
            (1<<1&$status['status']) and $this->response(['error'=>'文章已关闭评论'], 403);

            //组合数据
            $data = [
                'user_id'=>$userArr['id'],
                'comment'=>$comment,
                'article_id'=>$aid,
            ];
            $result = $this->articles_model->commentsAdd($data);
            if($result){
                $result['user']['id'] = $userArr['id'];
                $result['user']['name'] = $userArr['name'];
                $this->response($result, 200);
            }else{
                $this->response(['error'=>'评论失败'], 400);
            }
        }
    }

    /**
     * 编辑文章
     * @param null $aid
     * @param null $type
     * @param null $floor
     */
    public function index_put($aid = null, $type = null, $floor = null): void
    {
        //校验权限
        $token = $this->input->get_request_header('Access-Token', TRUE);
        $user_info = $this->parsing_token($token);

        //处理URL变量
        $aid_null = is_null($aid);
        $type_null = is_null($type);
        $floor_null = is_null($floor);
        $aid = intval($aid);
        $floor = intval($floor);

        if(!$aid_null && $type_null && $floor_null){      //编辑文章
            /* 获取POST数据 */
            $title = trim($this->put('title'));
            $content = trim($this->put('content'));
            $content_txt = str_replace('&nbsp;','',strip_tags($content));

            //验证POST数据
            empty($title) and $this->response(['error'=>'文章标题不能为空'], 400);
            mb_strlen($title) > 60 and $this->response(['error'=>'标题不能超过60个字符'], 400);
            empty($content) and $this->response(['error'=>'文章正文不能为空'], 400);
            mb_strlen($content_txt) > 5000 and $this->response(['error'=>'文章正文不能超过5000个字符'], 400);

            //权限验证
            $oinfo = $this->articles_model->articleInfoStatus(['ab.id'=>$aid], 'ab.author_id, as.status');
            count($oinfo) <= 0 and $this->response(['error'=>'文章不存在'], 404);
            $user_info->user_id != $oinfo[0]['author_id'] and $this->response(['error'=>'没有权限操作'], 403);
            (1<<2&$oinfo[0]['status']) and $this->response(['error'=>'文章已被删除'], 410);

            //组合数据
            $data = [
                'id'=>$aid,
                'title'=>$title,
                'content'=>$content,
                'resume'=>substr($content_txt,0,90).'...'
            ];

            $result['id'] = $this->articles_model->articleUpd($data);
            if($result['id'] > 0){
                $this->response($result, 200);
            }else{
                $this->response(['error'=>'编辑失败'], 400);
            }
        }
    }

    /**
     * 删除文章评论
     * @param null $aid
     * @param null $type
     * @param null $floor
     */
    public function index_delete($aid = null, $type = null, $floor = null): void
    {
        //校验权限
        $token = $this->input->get_request_header('Access-Token', TRUE);
        $user_info = $this->parsing_token($token);

        //处理URL变量
        $aid_null = is_null($aid);
        $type_null = is_null($type);
        $floor_null = is_null($floor);
        $aid = intval($aid);
        $floor = intval($floor);

        if(!$aid_null && $type=='comments' && !$floor_null){        //删除评论
            $data = [
                'article_id'=>$aid,
                'floor'=>$floor
            ];

            //权限验证
            $comments_info  = $this->articles_model->commentsInfo($data, 'comment_id,user_id');
            count($comments_info) > 0 or $this->response(['error'=>'评论不存在'], 404);
            $article_info = $this->articles_model->articleInfo(['id'=>$aid],'author_id');
            count($article_info) > 0 or $this->response(['error'=>'没有权限操作'], 403);
            if(!$this->admins_model->isAdmin($user_info->user_id) && $user_info->user_id != $comments_info[0]['user_id'] && $user_info->user_id != $article_info[0]['user_id']){
                $this->response(['error'=>'没有权限操作'], 403);
            }

            $result = $this->articles_model->commentsDel($comments_info[0]['comment_id']);
            if($result){
                $this->response(['error'=>'删除成功'], 200);
            }else{
                $this->response(['error'=>'删除失败'], 400);
            }
        }

    }



    /**
     * 点赞文章
     * @param $post_id
     */
    public function approval_post($article_id): void
    {
        echo $article_id;

        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            // 'user_id'=>3,
            'article_id'=>$article_id,
        );

        if($data['user_id']){

            //获取文章点赞状态，并点赞，取消点赞，点赞数目
            $rs = $this->articles_model->get_approval_post($data);


            if($rs){
                $this->articles_model->update_approval_post($data)?
                    $this->response(['success'=>'(取消)点赞成功'],200):
                    $this->response(['error'=>'操作失败'],400);
            }else{
                $this->articles_model->add_approval_post($data)?
                    $this->response(['success'=>'点赞成功'],204):
                    $this->response(['error'=>'点赞失败'],400);
            }
        }else{
            $this->response(['error'=>'未登录，不能操作'],401);
        }
    }


    /**
     *  A1 首页文章接口 用于展示首页文章
     * @param  [type] $offset 当前起始数
     * @param  [type] $limit 每页数量
     */
    public function articles_get(): void
    {
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        if(!empty($jwt)){
            $this->response(['error'=>'jwt为空']);
        }else{
            $token = $this->parsing_token($jwt);
            $offset = $token->offset;
            $limit = $token->limit;
        }
        $data = [
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
        ];

        $re['data'] = $this->articles_model->get_articles($data);
        if (!$re['data']) {
            $this->response(['error'=>'获取用户文章列表失败'], 400);
        }

         $this->response($re);
    }


    /**
     * 锁定文章(A10)
     * @param $post_id
     * POST /articles/:id/lock
     */
    public function lock_post($article_id): void
    {

        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'article_id'=>$article_id,
        );


        // $this->form_validation->set_data($data);
        // if ($this->form_validation->run('post_reply') == FALSE)
        //     $this->response(['error'=>validation_errors()],422);

        $article_exist = $this->articles_model->exist_article_post($data);

        if(empty($article_exist)){
            $this->response(['error'=>'该文章不存在！'],404);
        }

        //判断数据库中是否有记录
        $article_info = $this->articles_model->get_status_post($data['article_id']);



        // if($article_info['status'] == 1){
        //     $this->response(['error'=>'该文章已被锁定！'],409);
        // }
        if(($article_info['status']) & (1<<1)){
            $this->response(['error'=>'该文章已被锁定！'],409);
        }

        // if($article_info['status'] == 2){
        //     $this->response(['error'=>'该文章已被删除！'],410);
        // }
        if(($article_info['status']) & (1<<2)){
            $this->response(['error'=>'该文章已被删除！'],410);
        }

        if(empty($article_info)){
            $this->articles_model->lock_post($data['article_id'])?
            $this->response(['success'=>'锁定成功'],204):
            $this->response(['error'=>'锁定失败'],400);
        }



        //判断锁定权限并锁定文章，未写 未确定锁定需要管理员权限类别
        // $re = $this->judge_authority($token->user_id,$post_id,$post_info['group_base_id']);
        // if($re['lock_right']===1){
        //     $this->Post_model->lock_post($post_id)?
        //         $this->response(['success'=>'锁定成功'],200):
        //         $this->response(['error'=>'锁定失败'],400);
        // }else{
        //     $this->response(['error'=>'仅星球创建者和发帖者和管理员能锁定帖子!'],403);
        // }

    }

     /**
     * 删除文章(A11)
     * @param $article_id
     * DELETE /articles/:id
     */
    public function articles_delete($article_id): void
    {

        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'article_id'=>$article_id,
        );


        //判断数据库中是否有该文章
        $article_exist = $this->articles_model->exist_article_post($data);
        if(empty($article_exist)){
            $this->response(['error'=>'该文章不存在！'],404);
        }

        //判断数据库中是否有记录
        $article_info = $this->articles_model->get_status_post($data['article_id']);


        // if(){
        //     $this->response(['error'=>'该文章已被锁定！'],409);
        // }
        var_dump(($article_info['status']));
        var_dump(($article_info['status']) & (1<<2));
        if(($article_info['status']) & (1<<2)){
            $this->response(['error'=>'该文章已被删除！'],410);
        }

        if(empty($article_info) || (($article_info['status']) & (1<<1))){

            $this->articles_model->delete_post($data['article_id'],$article_info)?
            $this->response(['success'=>'删除成功'],204):
            $this->response(['error'=>'删除失败'],400);
        }

    }

    /**
     * 收藏文章
     */
    public function collections_put($user_id) :void
    {
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
            'article_id'=>$this->put('article_id'),
        );

        // $this->form_validation->set_data($data);
        // if ($this->form_validation->run('collect_post') == FALSE)
        //     $this->response(['error'=>validation_errors()],422);

        // $post_exist = $this->Common_model->judge_post_exist($data['article_id']);
        $article_exist = $this->articles_model->exist_article_post($data);
        if(!$article_exist){
            $this->response(['error'=>'该文章不存在！'],404);
        }
        $exist = $this->articles_model->check_collections_post($data);

        if($exist){
            $this->articles_model->delete_collections_post($exist)?
                $this->response(['success'=>'(取消)收藏成功'],203):
                $this->response(['error'=>'(取消)收藏失败']);
        }else{
            if($article_exist&&$this->articles_model->collections_post($data)){
                $this->response(['success'=>'收藏成功'],204);
            }else{
                $this->response(['error'=>'收藏失败，文章可能不存在']);
            }
        }
    }








    /*
     * A4 文章详情 文章详情-文章内容 GET /articles/:id

     * @param  [type] $article_id [description]
     * @return [type]             [description]
     */
    public function article_get($article_id)
    {

        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        if(!empty($jwt)){
            $this->response(['error'=>'查看文章详情失败']);
        }else{
            $token = $this->parsing_token($jwt);
           // $article_id = $token->article_id;
        }

        $re = $this->articles_model->get_article($article_id);
        if(!isset($re))
        {
            $this->response(['error'=>'查看文章详情失败']);
        }

        if ($re['articles']['approved_num'] > 0 )
        {
            $re['articles']['approved'] = TRUE;
        }
        else
        {
            $re['articles']['approved'] = False;
        }

        if ($re['articles']['collected_num'] > 0 )
        {
            $re['articles']['collected'] = TRUE;
        }
        else
        {
            $re['articles']['collected'] = False;
        }

        //判断文章状态  0正常  1被锁定  2被删除
        if ($re['articles']['status'] == '1')
        {
            $re['articles']['lock'] = TRUE;
        }
        elseif ($re['articles']['status'] == '2' )
        {
            $this->response(['error'=>'文章已被删除'],410);
        }
        else
        {
            $re['articles']['lock'] = False;
        }
        unset($re['articles']['status']);


         $this->response($re);

    }
    /**
     * A5 文章评论列表
     */

    public function comments_get($article_id)
    {

        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        if(!empty($jwt)){
            $this->response(['error'=>'jwt为空']);
        }else{
            $token = $this->parsing_token($jwt);
            $offset = $token->offset;
            $limit = $token->limit;
        }
        $data = [
            'article_id' => $article_id,
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
        ];


        $re = $this->articles_model->get_comments($data);
        $this->response($re);
    }


}