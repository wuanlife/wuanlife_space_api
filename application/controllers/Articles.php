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
        $this->load->library(array('form_validation','jwt'));
    }

    public function index_get()
    {
        echo "123";
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
            $title = trim($this->input->post('title'));
            $content = trim($this->input->post('content'));
            $content_txt = str_replace('&nbsp;','',strip_tags($content));
            $image_urls = $this->input->post('image_urls');
            $image_urls_arr = explode(',', $image_urls);

            //验证POST数据
            !empty($title) or $this->response(['error'=>'文章标题不能为空'], 400);
            mb_strlen($title) <= 60 or $this->response(['error'=>'标题不能超过60个字符'], 400);
            !empty($content) or $this->response(['error'=>'文章正文不能为空'], 400);
            mb_strlen($content_txt) <= 5000 or $this->response(['error'=>'文章正文不能超过5000个字符'], 400);
            count($image_urls_arr)<=3 or $this->response(['error'=>'至多三张预览图片'], 400);

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
            $comment = trim($this->input->post('comment'));

            //验证POST数据
            !empty($comment) or $this->response(['error'=>'回复内容不能为空'], 400);
            mb_strlen($comment) <= 5000 or $this->response(['error'=>'回复内容不能超过5000个字符'], 400);

            //组合数据
            $data = [
                'user_id'=>1,
                'comment'=>'1111',
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
            $title = trim($this->input->input_stream('title'));
            $content = trim($this->input->input_stream('content'));
            $content_txt = str_replace('&nbsp;','',strip_tags($content));

            //验证POST数据
            !empty($title) or $this->response(['error'=>'文章标题不能为空'], 400);
            mb_strlen($title) <= 60 or $this->response(['error'=>'标题不能超过60个字符'], 400);
            !empty($content) or $this->response(['error'=>'文章正文不能为空'], 400);
            mb_strlen($content_txt) <= 5000 or $this->response(['error'=>'文章正文不能超过5000个字符'], 400);

            //权限验证
            $oinfo = $this->articles_model->articleInfoStatus(['ab.id'=>$aid], 'ab.author_id, as.status');
            count($oinfo) > 0 or $this->response(['error'=>'文章不存在'], 404);
            $user_info->user_id == $oinfo['author_id'] or $this->response(['error'=>'没有权限操作'], 403);
            !(1&$oinfo['status']) or $this->response(['error'=>'没有权限操作'], 403);
            !(2&$oinfo['status']) or $this->response(['error'=>'文章已被删除'], 410);

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
                $this->response(['error'=>'修改失败'], 400);
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
            $comments_info  = $this->articles_model->commentsInfo($data, 'comment_id,user_id')[0];
            count($comments_info) > 0 or $this->response(['error'=>'评论不存在'], 404);
            $user_info->user_id == $comments_info['user_id'] or $this->response(['error'=>'没有权限操作'], 403);

            $result = $result['id'] = $this->articles_model->commentsDel($comments_info['comment_id']);
            if($result){
                $this->response(['error'=>'删除成功'], 200);
            }else{
                $this->response(['error'=>'删除失败'], 400);
            }
        }

    }

    /**
     *  A1 首页文章接口 用于展示首页文章
     * @param  [type] $offset 当前起始数
     * @param  [type] $limit 每页数量
     */
    public function articles_get()
    {
        //校验权限00
        // $jwt = $this->input->get_request_header('Access-Token', TRUE);
        // if(empty($jwt)){
        //     $user_id = NULL;
        // }else{
        //     $token = $this->parsing_token($jwt);
        //     $user_id = $token->user_id;
        // }

        // $data = array(
        //   //  'user_id'   => $user_id?:0,
        //     'limit'     => $this->get('limit')?:20,     //每页显示数
        //     'offset'    => $this->get('offset')?:0,     //每页起始数
        // );

         $re['data'] = $this->articles_model->get_Articles();
        // $this->Articles_model->get_Articles();
         $this->response($re);


    }


    /**
     * A4 文章详情 文章详情-文章内容 GET /articles/:id

     * @param  [type] $article_id [description]
     * @return [type]             [description]
     */
    public function article_get($article_id)
    {
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
        //$data['article_id'] = $article_id;
        $re = $this->articles_model->get_comments($article_id);
        $this->response($re);
    }

}