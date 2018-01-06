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
    }

//    /**
//     * 解析jwt，获得用户id（旧的拷贝过来的）
//     * @param $jwt
//     * @return mixed
//     */
//    private function parsing_token($jwt)
//    {
//        try{
//            $token = $this->jwt->decode($jwt,$this->config->item('encryption_key'));
//            return $token;
//        }
//        catch(InvalidArgumentException $e)
//        {
//            return $this->response(['error'=>'未登录，不能操作'],401);
//        }
//        catch(UnexpectedValueException $e)
//        {
//            return $this->response(['error'=>'未登录，不能操作'],401);
//        }
//        catch(DomainException $e)
//        {
//            return $this->response(['error'=>'未登录，不能操作'],401);
//        }
//    }

    /**
     * 搜索文章
     */
    public function search_post(): void
    {
        // 加载搜索模版
        $this->load->model('search_model');
        // 获取url参数列表
        $param = $this->search_model->getSearchParam();
        // 判断参数列表是否完整
        $this->search_model->validateSearchParam($param) or $this->response(['error' => '缺少必要的参数'], 400);
        // 获取相匹配的数据
        $data = $this->search_model->search($param, 'articles');
        // 返回数据
        $this->response($data, 200);
    }

    /**
     * 发表文章/发表评论/编辑文章/删除文章评论
     * @param null $aid
     * @param null $type
     * @param null $floor
     */
    public function index_post($aid = null, $type = null, $floor = null): void
    {
//        //校验权限
//        $token = $this->input->get_request_header('Access-Token', TRUE);
//        $user_info = $this->parsing_token($token);

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
                'user_id'=>$user_info->user_id,
                'user_name'=>$user_info->user_name,
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
        }else if(!$aid_null && $type_null && $floor_null){      //编辑文章
            /* 获取POST数据 */
            $title = trim($this->input->post('title'));
            $content = trim($this->input->post('content'));
            $content_txt = str_replace('&nbsp;','',strip_tags($content));

            //验证POST数据
            !empty($title) or $this->response(['error'=>'文章标题不能为空'], 400);
            mb_strlen($title) <= 60 or $this->response(['error'=>'标题不能超过60个字符'], 400);
            !empty($content) or $this->response(['error'=>'文章正文不能为空'], 400);
            mb_strlen($content_txt) <= 5000 or $this->response(['error'=>'文章正文不能超过5000个字符'], 400);

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
                $result['user']['id'] = $user_info->user_id;
                $result['user']['name'] = $user_info->user_name;
                $this->response($result, 200);
            }else{
                $this->response(['error'=>'评论失败'], 400);
            }
        }
    }

    public function index_put($aid = null, $type = null, $floor = null): void
    {
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

    public function index_delete($aid = null, $type = null, $floor = null): void
    {
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
            $id  = $this->articles_model->commentsOneId($data);
            $id!=0 or $this->response(['error'=>'评论不存在'], 400);

            $data = [
                'article_id'=>$aid,
                'floor'=>$floor
            ];
            $result = $result['id'] = $this->articles_model->commentsDel($id);
            if($result){
                $this->response(['error'=>'删除成功'], 200);
            }else{
                $this->response(['error'=>'删除失败'], 400);
            }
        }

    }

}