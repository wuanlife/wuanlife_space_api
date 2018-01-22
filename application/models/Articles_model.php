<?php
/**
 * Created by PhpStorm.
 * User: moe
 * Date: 2018/1/6
 * Time: 21:16
 */

class Articles_model extends CI_Model
{
    private $a_base = 'articles_base';
    private $a_content = 'articles_content';
    private $a_status = 'articles_status';
    private $a_status_detail = 'a_status_detail';
    private $a_approval = 'articles_approval';
    private $a_approval_count = 'articles_approval_count';
    private $a_comments = 'articles_comments';
    private $a_comments_count = 'articles_comments_count';
    private $c_contents = 'comment_contents';   //评论内容表
    private $i_url = 'image_url';   //文章图片url表
    public function __construct()
    {
        parent::__construct();
    }

    public function articleOne()
    {

    }
    /**
     * 获得文章内容
     * @param $data
     * @param string $field
     * @return array
     */
    public function articleInfo($data, $field='*'):array
    {
        $res = $this->db
            ->select($field)
            ->from($this->a_base)
            ->where($data)
            ->get()
            ->result_array();
        return $res;
    }

    /**
     * 获得文章内容及权限
     * @param $data
     * @param string $field
     * @return array
     */
    public function articleInfoStatus($data, $field='*'):array
    {
        $res = $this->db
            ->select($field)
            ->from($this->a_base.' ab')
            ->join($this->a_status.' as', 'ab.id = as.id', 'left')
            ->where($data)
            ->get()
            ->result_array();
        return $res;
    }

    /**
     * 添加文章
     * @param $data
     * @return int
     */
    public function articleAdd($data): int
    {
        $id = 0;
        //回滚 start
        $this->db->trans_start();
        //添加文章表
        $data_base = [
            'author_id'=>$data['user_id'],
            'author_name'=>$data['user_name'],
            'content_digest'=> $data['resume'],
            'create_at'=>date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->a_base, $data_base);
        $id = $this->db->insert_id();

        //添加文章内容表
        $data_content = [
            'id'=>$id,
            'title'=>$data['title'],
            'content'=>$data['content'],
        ];
        $this->db->insert($this->a_content, $data_content);

        //添加文章状态表
        $data_status = [
            'id'=>$id,
            'status'=>0,
            'create_at'=>date('Y-m-d H:i:s')
        ];
        $this->db->insert($this->a_status, $data_status);

        //添加文章图片url
        $data_url = [];
        foreach($data['image_urls_arr'] as $k => $v){
            $data_url[$k] = [
                'article_id'=>$id,
                'url'=>$v
            ];
        }
        $this->db->insert_batch($this->i_url, $data_url);

        //结束 end
        $this->db->trans_complete();
        return $id;
    }

    /**
     * 修改文章内容
     * @param $data
     * @return int
     */
    public function articleUpd($data): int
    {
        //回滚 start
        $this->db->trans_start();

        //添加文章表
        $data_base_where = [
            'id'=>$data['id'],
        ];
        $data_base_data = [
            'content_digest'=>$data['resume'],
        ];
        $this->db->where($data_base_where)->update($this->a_base, $data_base_data);
        $id = $this->db->insert_id();

        //添加文章内容表
        $data_content_where = [
            'id'=>$data['id'],
        ];
        $data_content_data = [
            'title'=>$data['title'],
            'content'=>$data['content'],
        ];
        $this->db->where($data_content_where)->update($this->a_content, $data_content_data);

        //结束 end
        $this->db->trans_complete();
        return $data['id'];
    }

    /**
     * 获取文章评论数量缓存
     * @param $id
     * @return int
     */
    public function commentsCount($id): int
    {
        $res = $this->db->select('count')->from($this->a_comments_count)->where('articles_id', $id)->get();
        $result = $res->row();
        if(empty($result)){
            return 0;
        }else{
            return $result->count;
        }
    }

    /**
     * 获得评论
     * @param $data
     * @param string $field
     * @return array
     */
    public function commentsInfo($data, $field='*'):array
    {
        $res = $this->db
            ->select($field)
            ->from($this->a_comments)
            ->where($data)
            ->get()
            ->result_array();
        return $res;
    }

    /**
     * 添加评论
     * @param $data
     * @return array|bool
     */
    public function commentsAdd($data)
    {
        $id = 0;
        //获得现有楼层
        $count = $this->commentsCount($data['article_id']);

        //回滚 start
        $this->db->trans_start();

        //添加文章评论表
        $now_time = date('Y-m-d H:i:s');
        $data_comments = [
            'article_id'=>$data['article_id'],
            'user_id'=>$data['user_id'],
            'floor'=>$count+1,
            'create_at'=>$now_time
        ];
        $res1 = $this->db->insert($this->a_comments, $data_comments);
        $id = $this->db->insert_id();

        //添加文章内容表
        $data_c_contents = [
            'id'=>$id,
            'content'=>$data['comment']
        ];
        $res2 = $this->db->insert($this->c_contents, $data_c_contents);
        //回滚 end
        $this->db->trans_complete();

        if($res1 && $res2){
            $result = [
                'comment'=>$data['comment'],
                'update_at'=>$now_time,
                'create_at'=>$now_time,
                'floor'=>$count+1
            ];
            return $result;
        }else{
            return false;
        }
    }

    /**
     * 删除评论
     * @param $id
     * @return bool
     */
    public function commentsDel($id)
    {
        //回滚 start
        $this->db->trans_start();

        //删除评论基础信息
        $data_comments = [
            'comment_id'=>$id
        ];
        $res1 = $this->db->where($data_comments)->delete($this->a_comments);

        //删除文章评论数缓存信息
        $data_contents = [
            'id'=>$id
        ];
        $res2 = $this->db->where($data_contents)->delete($this->c_contents);

        //回滚 end
        $this->db->trans_complete();

        return ($res1&&$res2);
    }



    /*
     * 获得用户点赞
     * @param $data
     * @return mixed
     */
    public function get_approval_post($data)
    {
        //查询数据库中对应文章点赞情况
        $sql=$this->db->select('*')
            ->from('articles_approval')
            ->where('article_id',$data['article_id'])
            ->where('user_id',$data['user_id'])
            ->get()
            ->row_array();
      return $sql;
    }



    /**
     * 更新点赞
     * @param $data
     * @return bool
     */
    public function update_approval_post($data)
    {
        //获取点赞状态
        $approved = $this->get_approval_post($data);

        if($approved['user_id']){

            //文章总赞数减一
            $res1 = $this->db->set('count','count-1',false)
                         ->where('article_id',$data['article_id'])
                         ->update('articles_approval_count');
            //取消用户对应文章点赞
            $res2 = $this->db->delete('articles_approval',$approved);
            //返回点赞成功
            return true;
        }else{
            //调用时，无user_id时，操作失败
            return $this->response(['error'=>'操作失败'],400);
        }

    }

    /**
     * 增加点赞
     * @param $data
     * @return bool
     */
    public function add_approval_post($data)
    {
        $field = array(
            'user_id' => $data['user_id'],
            'article_id' => $data['article_id'],
        );
        //在articles_approval表中添加点赞数据：user_id和文章_id
        return $this->db->insert('articles_approval',$field);
    }



    /*
     * 获得文章状态，为（A10）（A11）（A12）做准备
     * @param $data
     * @return mixed
     */

    public function get_status_post($data)
    {
        //获取文章的状态
        $sql=$this->db->select('*')
            ->from('articles_status')
            ->where('id',$data)
            ->get()
            ->row_array();
        return $sql;
    }


    /*
     * 锁定文章(A10)
     * @param $data
     * @return mixed
     */
    public function lock_post($data)
    {
        //锁定文章
        $field = array(
            'id' => $data,
            'status' => 1<<1
            );
        $sql = $this->db->insert('articles_status',$field);

        return $sql;
    }



    /**
     * 删除文章
     * @param $data
     * @return bool
     */
    public function delete_post($data,$article_info){

        //如果文章有状态执行叠加操作
        if($article_info){

            $sql = $this->db->set('status',($article_info['status'] | (1<<2)),false)
                    ->where('id', $data)
                    ->update('articles_status');

            return $sql;
        }else{                    //如果文章不存在状态，添加删除状态
            $field = array(
                'id' => $data,
                'status' => 1<<2
                );
            $sql = $this->db->insert('articles_status',$field);
            return $sql;
        }
    }

    /**
     * 判断文章是否存在
     * @param $data
     * @return mixed
     */

    public function exist_article_post($data){

        $sql=$this->db->select('*')
            ->from('articles_content')
            ->where('id',$data['article_id'])
            ->get()
            ->row_array();

        return $sql;
    }

    /**
     * 判断用户是否收藏该文章
     * @param $data
     * @return mixed
     */

    public function check_collections_post($data){

        $sql=$this->db->select('*')
            ->from('user_collections')
            ->where('user_id',$data['user_id'])
            ->where('article_id',$data['article_id'])
            ->get()
            ->row_array();

        return $sql;
    }


    /**
     * 收藏文章
     * @param $data
     * @return bool
     */
    public function collections_post($data){

        $i_data=array(
            'user_id'=>$data['user_id'],
            'article_id'=>$data['article_id']
        );
        return $this->db->insert('user_collections',$i_data);
    }

    /**
     * 取消收藏文章
     * @param $data
     * @return bool
     */
    public function delete_collections_post($data){

        $sql=$this->db->delete('user_collections',$data);

        return $sql;
    }


    /**
     * A1 获取页面文章数据
     * @param array $data
     * @param bool $spaging
     * @return array
     */
    public function get_articles($data)
    {
    {
        $select = ' articles_base.id,
                    articles_content.title,
                    articles_content.content,
                    articles_base.update_at,
                    articles_base.create_at,
                    articles_base.author_name,
                    articles_base.author_id,
                    articles_status.status
        ';
        $this->db->select("$select");
        $this->db->from('articles_base');
        $this->db->join('articles_content',' articles_content.id = articles_base.id');
        $this->db->join('users_base','users_base.name = articles_base.author_name');
        $this->db->join('articles_status','articles_status.id = articles_base.id');
        $this->db->where("articles_status.status != 2"); //被删除的文章不显示
        $this->db->limit($data['limit'],$data['offset']);
        $re['articles'] = $this->db->get()->result_array();

        foreach ($re['articles'] as $key => $value) {

            //获取文章评论数
            $re['articles'][$key]['replied_num'] = (int)$this->db->select('articles_comments_count.count')->from('articles_comments_count')->where("articles_comments_count.articles_id = {$re['articles'][$key]['id']}")->get()->row()->count;

            //获取文章点赞数
            $re['articles'][$key]['approved_num'] = (int)$this->db->select('articles_approval_count.count')->from('articles_approval_count')->where("articles_approval_count.article_id = {$re['articles'][$key]['id']}")->get()->row()->count;

            //获取文章收藏数
            $re['articles'][$key]['collected_num'] = (int)$this->db->select('user_collections.user_id')->from('user_collections')->where("article_id = {$re['articles'][$key]['id']}")->get()->num_rows();

            //获取文章作者id name avatar_url
            $re['articles'][$key]['author']['id'] = (int)$re['articles'][$key]['author_id'];
            $re['articles'][$key]['author']['name'] = $re['articles'][$key]['author_name'];
            $re['articles'][$key]['author']['avatar_url'] = $this->db->select('avatar_url.url')->from('avatar_url')->where("avatar_url.user_id = {$re['articles'][$key]['author_id']}")->get()->row()->url;

            //id 转成int
            $re['articles'][$key]['id'] = (int)$re['articles'][$key]['id'];

            //日期转成iso格式
            $re['articles'][$key]['update_at'] = date('c',strtotime($re['articles'][$key]['update_at']));
            $re['articles'][$key]['create_at'] = date('c',strtotime($re['articles'][$key]['create_at']));

            if ($re['articles'][$key]['approved_num'] > 0 )
            {
                $re['articles'][$key]['approved'] = TRUE;
            }
            else
            {
                $re['articles'][$key]['approved'] = False;
            }

            if ($re['articles'][$key]['collected_num'] > 0 )
            {
                $re['articles'][$key]['collected'] = TRUE;
            }
            else
            {
                $re['articles'][$key]['collected'] = False;
            }

            if ($re['articles'][$key]['replied_num'] > 0 )
            {
                $re['articles'][$key]['replied'] = TRUE;
            }
            else
            {
                $re['articles'][$key]['replied'] = False;
            }
            $data['article_id'] = $re['articles'][$key]['id'];
            $re['articles'][$key]['image_urls'] = $this->users_model->get_article_img($data);

            unset($re['articles'][$key]['author_id']);

        }

        //au
        // $month_start_time = date('y-m-01 00-00-00');
        // $time = date('y-m-d h-i-s');

        // $query = "SELECT author_id as id,author_name as name,COUNT(0) AS monthly_articles_num FROM articles_base where create_at between '{$month_start_time}' and '{$time}' GROUP BY author_id desc HAVING COUNT(author_id) order by monthly_articles_num desc";

        // $re['au'] = $this->db->query($query)->result_array();

        // foreach ($re['au'] as $key => $value) {

        // $re['au'][$key]['avatar_url'] = $this->db->select('avatar_url.url')->from('avatar_url')->where("avatar_url.user_id = {$re['au'][$key]['id']}")->get()->row()->url; 
        // }
        
        //获取文章总数
        $select = ' articles_base.id,
                    articles_content.title,
                    articles_content.content,
                    articles_base.update_at,
                    articles_base.create_at,
                    articles_base.author_name,
                    articles_base.author_id,
                    articles_status.status
        ';
        $this->db->select("$select");
        $this->db->from('articles_base');
        $this->db->join('articles_content',' articles_content.id = articles_base.id');
        $this->db->join('users_base','users_base.name = articles_base.author_name');
        $this->db->join('articles_status','articles_status.id = articles_base.id');
        $this->db->where("articles_status.status != 2"); //被删除的文章不显示
        $re['total'] = $this->db->get()->num_rows();

        foreach ($re['articles'] as $key => $value) {
            unset($re['articles'][$key]['author_name']);
            unset($re['articles'][$key]['status']);
        }

        return $re;

    }
    }


    /**
     * A4 文章详情 文章内容
     * @param  [type] $article_id [description]
     * @return [type]             [description]
     */
    public function get_article($article_id):array
    {
        $select = ' articles_base.id,
                    articles_content.title,
                    articles_content.content,
                    articles_base.update_at,
                    articles_base.create_at,
                    articles_base.author_name,
                    articles_status.status
        ';
        $this->db->select($select);
        $this->db->from('articles_base');
        $this->db->where("articles_base.id = {$article_id}");
        $this->db->join('articles_content',' articles_content.id = articles_base.id');
        $this->db->join('users_base','users_base.name = articles_base.author_name');
        $this->db->join('articles_status','articles_status.id = articles_base.id');

        $re = $this->db->get()->row_array();
        // print_r($re['articles']);
        // exit;
        // if ($re['articles'] = null) {
        //     return 0;
        // }

        // $data['article_id'] = $article_id;
        //$re['articles']['image_urls'] = $this->users_model->get_article_img($data);

        //获取文章评论数
        // $re['articles']['replied_num'] = $this->db->select('articles_comments_count.count')->from('articles_comments_count')->where("articles_comments_count.articles_id = {$article_id}")->get()->row()->count;

        //article id 改成int
        $re['id'] = (int)$re['id'];

        //日期转成iso格式
        $re['update_at'] = date('c',strtotime($re['update_at']));
        $re['create_at'] = date('c',strtotime($re['create_at']));

        //获取文章点赞数
        $re['approved_num'] = (int)$this->db->select('articles_approval_count.count')->from('articles_approval_count')->where("articles_approval_count.article_id = {$article_id}")->get()->row()->count;

        //获取文章作者id
        $re['author_id'] = (int)$this->db->select('users_base.id')->from('users_base')->where("users_base.name = '{$re['author_name']}'")->get()->row()->id;

        //获取文章收藏数
        $re['collected_num'] = $this->db->select('user_collections.user_id')->from('user_collections')->where("article_id = {$article_id}")->get()->num_rows();

        //获取作者的id name 头像url 发表文章数
        $re['author']['id'] =  $re['author_id'];
        $re['author']['name'] = $re['author_name'];
        $re['author']['avatar_url'] = $this->db->select('avatar_url.url')->from('avatar_url')->where("avatar_url.user_id = {$re['author']['id']}")->get()->row()->url;
        $re['author']['articles_num'] = (int)$this->db->select('users_articles_count.count')->from('users_articles_count')->where("users_articles_count.user_id = {$re['author']['id']}")->get()->row()->count;
        unset($re['author_id']);
        unset($re['author_name']);

        return $re;
    
    }


    /**
     * A5 文章评论列表
     *
     * @return [type] [description]
     */
    public function get_comments($data)
    {
        $select = ' articles_comments.user_id,
                    articles_comments.floor,
                    articles_comments.create_at,
                    comment_contents.content as comment,
                    users_base.name as user_name,
                    ';
        $this->db->select($select);
        $this->db->from('articles_comments');
        $this->db->join('comment_contents','comment_contents.id = articles_comments.comment_id');
        $this->db->join('users_base','users_base.id = articles_comments.user_id');
        $this->db->where("articles_comments.article_id ={$data['article_id']}");
        $this->db->limit($data['limit'],$data['offset']);
        $re = $this->db->get()->result_array();

        foreach ($re as $key => $value) {
            $re[$key]['user_id'] =(int)$re[$key]['user_id'];
            $re[$key]['floor'] = (int)$re[$key]['floor'];
            $re[$key]['create_at'] = date('c',strtotime($re[$key]['create_at']));
        }
        $re['total'] = $this->db->select('*')->from('articles_comments')->where('article_id',$data['article_id'])->get()->num_rows();
        return $re;


    }

}