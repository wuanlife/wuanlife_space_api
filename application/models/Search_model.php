<?php
/**
 * Created by PhpStorm.
 * User: tacer
 * Date: 2017/12/28
 * Time: 20:03
 */

class Search_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取搜索所需参数
     * @return array
     */
    public function getSearchParam(): array
    {
        return
            [
                'keyword' => $this->input->get('keyword'),
                'offset' => $this->input->get('offset'),
                'limit' => $this->input->get('limit')
            ];
    }

    /**
     * 验证参数是否合法
     * @param array $data
     * @return bool
     */
    public function validateSearchParam(array $data): bool
    {
        foreach ($data as $v) {
            if ($v === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * 根据关键字进行搜索
     * @param array  $data
     * @param string $aim
     * @return array
     */
    public function search(array $data, string $aim): array
    {
        if ($aim == 'users') {
            return $this->search_users($data);
        } elseif ($aim == 'articles') {
            return $this->search_articles($data);
        } else {
            return [];
        }
    }

    /**
     * 搜索相关用户
     * @param array $data
     * @return array
     */
    public function search_users(array $data): array
    {
        $i = 0;
        $result = [];
        $res =
            $this->db
                ->select('id,url,mail,name')
                ->from('users_base')
                ->join('avatar_url', 'users_base.id = avatar_url.user_id', 'left')
                ->like('name', $data['keyword'])
                ->limit($data['limit'], $data['offset'])
                ->get();

        foreach ($res->result() as $row) {
            $result[$i]['id'] = (int)$row->id;
            $result[$i]['avatar_url'] = $row->url;
            $result[$i++]['name'] = $row->name;
        }

        $total = $res->num_rows();

        return ['users' => $result, 'total' => $total];
    }

    /**
     * 搜索相关文章
     * @param $data
     * @return array
     */
    public function search_articles($data): array
    {
        $i = 0;
        $result = [];
        $res =
            $this->db
                ->select('title,content,articles_base.id,update_at,create_at')
                ->from('articles_content')
                ->join('articles_base', 'articles_base.id = articles_content.id', 'inner')
                ->or_like(['title' => $data['keyword'], 'content' => $data['keyword']])
                ->limit($data['limit'], $data['offset'])
                ->get();

        foreach ($res->result() as $row) {
            $result[$i]['title'] = $row->title;
            $result[$i]['content'] = $row->content;
            $result[$i]['author_id'] = (int)$row->id;
            $result[$i]['update_at'] = date('c',strtotime($row->update_at));
            $result[$i++]['create_at'] = date('c',strtotime($row->create_at));
        }

        $total = $res->num_rows();

        return ['articles' => $result, 'total' => (int)$total];
    }

}