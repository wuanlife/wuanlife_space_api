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
            return $res->result()->count;
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
}