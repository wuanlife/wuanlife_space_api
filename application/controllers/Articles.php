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

            //正则出三条正文中的url地址
            //Gtaker 2018/2/1 17:00
            $image_urls_arr = [];
            $i = -1;
            $content = preg_replace_callback(
                "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/",
                function ($matches) use (&$image_urls_arr,&$i){
                    if ($i < 3){
                        $i++;
                        $arr[] = $matches[0];
                        return '[图片]';
                    }else {
                        return $matches[0];
                    }
                },$content);

            //验证POST数据
            empty($title) and $this->response(['error'=>'文章标题不能为空'], 400);
            mb_strlen($title) > 60 and $this->response(['error'=>'标题不能超过60个字符'], 400);
            empty($content) and $this->response(['error'=>'文章正文不能为空'], 400);
            mb_strlen($content_txt) > 5000 and $this->response(['error'=>'文章正文不能超过5000个字符'], 400);
            //count($image_urls_arr) > 3 and $this->response(['error'=>'至多三张预览图片'], 400);

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
                $result['user']['id'] = (int)$userArr['id'];
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
            if(!$this->admins_model->isAdmin($user_info->user_id)
                && $user_info->user_id != $comments_info[0]['user_id']
                && $user_info->user_id != $article_info[0]['author_id']
            ){
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
     * 点赞文章(A2)
     * @param $post_id
     * POST /articles/:id/approval
     */
    public function approval_post($article_id): void
    {

        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);


        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'article_id'=>$article_id,
        );

        if($data['user_id']){

            //获取文章点赞状态，并点赞，取消点赞，点赞数目
            $rs = $this->articles_model->get_approval_post($data);


            if(!$rs){
                $this->articles_model->add_approval_post($data)?
                    $this->response(['success'=>'点赞成功'],204):
                    $this->response(['error'=>'点赞失败'],400);
            }else{
                    $this->response(['error'=>'该文章您已点过赞'],400);
            }
        }else{
            $this->response(['error'=>'未登录，不能操作'],401);
        }
    }
    /**
     * 取消点赞文章(A15)
     * @param $post_id
     * DELETE /articles/:id/approval
     */
    public function approval_delete($article_id): void
    {

        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);


        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'article_id'=>$article_id,
        );

        if($data['user_id']){

            //获取文章点赞状态，并点赞，取消点赞，点赞数目
            $rs = $this->articles_model->get_approval_post($data);
            //如果用户已对该文章点赞过，则执行取消点赞
            if($rs){
                $this->articles_model->update_approval_post($data)?
                    $this->response(['success'=>'取消点赞成功'],204):
                    $this->response(['error'=>'操作失败'],400);
            }else{
                    $this->response(['error'=>'请先点赞后再执行此操作'],400);
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
        // $jwt = $this->input->get_request_header('Access-Token', TRUE);
        // if(empty($jwt)){
        //     $this->response(['error'=>'jwt为空']);
        // }
        // else{
        //     $token = $this->parsing_token($jwt);
        // }
        $data = [
            'limit'     => $this->get('limit') ?? 20,     //每页显示数
            'offset'    => $this->get('offset') ?? 0,     //每页起始数
            'order'     => $this->get('order') ?? 'asc',
        ];

        $re = $this->articles_model->get_articles($data);
        if (!$re) {
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


        //判断是不是管理员，不是管理员不具备操作权限
        if(!$this->admins_model->isAdmin($data['user_id'])){
             $this->response(['error'=>'没有权限操作'],403);
        }

        $article_exist = $this->articles_model->exist_article_post($data);

        if(empty($article_exist)){
            $this->response(['error'=>'该文章不存在！'],404);
        }

        //判断数据库中是否有记录
        $article_info = $this->articles_model->get_status_post($data['article_id']);


        if(($article_info['status']) & (1<<2)){
            $this->response(['error'=>'该文章已被删除！'],410);
        }

        if(($article_info['status']) & (1<<1)){
            $this->response(['error'=>'该文章已被锁定！'],400);
        }

        //临时修正BUG，插入状态码：1为插入，2为更新
        if(empty($article_info)){
            $this->articles_model->lock_post($data['article_id'],1)?
            $this->response(['success'=>'锁定成功'],204):
            $this->response(['error'=>'锁定失败'],400);
        }

        if(($article_info['status']) == 0){
            $this->articles_model->lock_post($data['article_id'],2)?
            $this->response(['success'=>'锁定成功'],204):
            $this->response(['error'=>'锁定失败'],400);
        }
    }

    /**
     * 取消锁定文章(A17)
     * @param $post_id
     * DELETE /articles/:id/lock
     */
    public function lock_delete($article_id): void
    {

        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);
        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'article_id'=>$article_id,
        );

        // 判断是不是管理员，不是管理员不具备操作权限
        if(!$this->admins_model->isAdmin($data['user_id'])){
             $this->response(['error'=>'没有权限操作'],403);
        }

        $article_exist = $this->articles_model->exist_article_post($data);

        if(empty($article_exist)){
            $this->response(['error'=>'该文章不存在！'],404);
        }

        //判断数据库中是否有记录
        $article_info = $this->articles_model->get_status_post($data['article_id']);


        if(($article_info['status']) & (1<<2)){
            $this->response(['error'=>'该文章已被删除！'],410);
        }

        if(($article_info['status']) & (1<<1)){
            $this->articles_model->clear_post($data['article_id'])?
            $this->response(['success'=>'取消锁定成功'],204):
            $this->response(['error'=>'取消锁定失败'],400);
        }else{
            $this->response(['error'=>'文章没有被锁定'],400);
        }

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

        // 获取文章的作者author_id
        $article_author_id = $this->articles_model->author_article_post($data);


        //判断是不是管理员，不是管理员不具备操作权限 （可以短路） 或  判断登陆人是文章作者本人
        if(   ($this->admins_model->isAdmin($data['user_id'])) || ($data['user_id'] == $article_author_id['author_id'])   ){


            //判断数据库中是否有记录
            $article_info = $this->articles_model->get_status_post($data['article_id']);


            if(($article_info['status']) & (1<<2)){
                $this->response(['error'=>'该文章已被删除！'],410);
            }

            if(empty($article_info) || (($article_info['status']) & (1<<1)) || ($article_info['status'] == 0)){

                $this->articles_model->delete_post($data['article_id'],$article_info,$article_author_id['author_id'])?
                $this->response(['success'=>'删除成功'],204):
                $this->response(['error'=>'删除失败'],400);
            }
        } else{
            $this->response(['error'=>'没有权限操作'],403);
        }

    }

    /**
     * 收藏文章(A12)
     * @param $user_id
     * PUT /users/:id/collections
     */
    public function collections_put($user_id) :void
    {
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //文档中有权限一项，验证提交id和登录id是否一样
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }


        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'article_id'=>$this->put('article_id'),
        );

        //检查文章是否存在
        $article_exist = $this->articles_model->exist_article_post($data);
        if(!$article_exist){
            $this->response(['error'=>'该文章不存在！'],404);
        }

        //检查文章是否是删除状态
        $article_info = $this->articles_model->get_status_post($data['article_id']);
        if(($article_info['status']) & (1<<2)){
            $this->response(['error'=>'该文章已被删除！'],410);
        }

        //检查文章是否在收藏列表中
        $exist = $this->articles_model->check_collections_post($data);
        if(!$exist){
            if($article_exist&&$this->articles_model->collections_post($data)){
                $this->response(['success'=>'收藏成功'],204);
            }else{
                $this->response(['error'=>'收藏失败'],400);
            }
        }else{
            $this->response(['error'=>'收藏失败，文章已被收藏过'],400);
        }
    }

    /**
     * 取消收藏文章(A16)
     * @param $user_id
     * DELETE /users/:id/collections
     */
    public function collections_delete($user_id) :void
    {
        //权限校验
        $jwt = $this->input->get_request_header('Access-Token', TRUE);
        $token = $this->parsing_token($jwt);

        //文档中有权限一项，验证提交id和登录id是否一样
        if($token->user_id!=$user_id)
        {
            $this->response(['error'=>'您没有权限'],403);
        }

        //输入参数校验
        $data=array(
            'user_id'=>$token->user_id,
            'article_id'=>$this->delete('article_id'),
        );

        //检查文章是否存在
        $article_exist = $this->articles_model->exist_article_post($data);
        if(!$article_exist){
            $this->response(['error'=>'该文章不存在！'],404);
        }

        //检查文章是否是删除状态
        $article_info = $this->articles_model->get_status_post($data['article_id']);
        if(($article_info['status']) & (1<<2)){
            $this->response(['error'=>'该文章已被删除！'],410);
        }

        $exist = $this->articles_model->check_collections_post($data);

        if($exist){
            $this->articles_model->delete_collections_post($exist)?
                $this->response(['success'=>'取消收藏成功'],204):
                $this->response(['error'=>'取消收藏失败'],400);
        }else{
                $this->response(['error'=>'取消收藏失败，文章没有被收藏'],404);
        }
    }






    /*
     * A4 文章详情 文章详情-文章内容 GET /articles/:id

     * @param  [type] $article_id [description]
     * @return [type]             [description]
     */
    public function article_get($article_id)
    {

        // $jwt = $this->input->get_request_header('Access-Token', TRUE);
        // if(empty($jwt)){
        //     $this->response(['error'=>'$jwt为空']);
        // }else{
        //     $token = $this->parsing_token($jwt);
        //    // $article_id = $token->article_id;
        // }
        $data['article_id'] = $article_id;
        $article_info = $this->articles_model->get_status_post($data['article_id']);

        if(empty($article_info)){
            $this->response(['error'=>'该文章不存在！'],404);
        }

        $re = $this->articles_model->get_article($article_id);
        if(!isset($re))
        {
            $this->response(['error'=>'查看文章详情失败']);
        }

        if ($re['approved_num'] > 0 )
        {
            $re['approved'] = TRUE;
        }
        else
        {
            $re['approved'] = False;
        }

        if ($re['collected_num'] > 0 )
        {
            $re['collected'] = TRUE;
        }
        else
        {
            $re['collected'] = False;
        }

        //判断文章状态  0正常  1被锁定  2被删除
        //更正判断逻辑，Gtaker 2018/2/1  17:25
        if ((($re['status'] >> 1) & 1) != 0)
        {
            $re['lock'] = TRUE;
        }
        elseif ((($re['status'] >> 2) & 1) != 0)
        {
            $this->response(['error'=>'文章已被删除'],410);
        }
        else
        {
            $re['lock'] = False;
        }
        unset($re['status']);


         $this->response($re);

    }
    /**
     * A5 文章评论列表
     */

    public function comments_get($article_id)
    {

        // $jwt = $this->input->get_request_header('Access-Token', TRUE);
        // if(empty($jwt)){
        //     $this->response(['error'=>'jwt为空']);
        // }else{
        //     $token = $this->parsing_token($jwt);
        // }
        $data = [
            'article_id' => $article_id,
            'limit'     => $this->get('limit')?:20,     //每页显示数
            'offset'    => $this->get('offset')?:0,     //每页起始数
        ];


        $re = $this->articles_model->get_comments($data);

        $this->response($re);
    }


}