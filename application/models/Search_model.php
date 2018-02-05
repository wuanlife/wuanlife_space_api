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
                'offset'  => $this->input->get('offset') ?? 0,
                'limit'   => $this->input->get('limit') ?? 21
            ];
    }

    /**
     * 验证参数是否合法
     *
     * @param array $data
     *
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
     *
     * @param array  $data
     * @param string $aim
     *
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
     *
     * @param array $data
     *
     * @return array
     */
    public function search_users(array $data): array
    {
        $i      = 0;
        $result = [];
        $res    =
            $this->db
                ->select('id,url,mail,name')
                ->from('users_base')
                ->join('avatar_url', 'users_base.id = avatar_url.user_id', 'left')
                ->like('name', $data['keyword'])
                ->limit($data['limit'], $data['offset'])
                ->get();

        foreach ($res->result() as $row) {
            $result[$i]['id']         = (int)$row->id;
            $result[$i]['avatar_url'] = $row->url;
            $result[$i++]['name']     = $row->name;
        }

        $total = $res->num_rows();

        return ['users' => $result, 'total' => $total];
    }

    /**
     * 搜索相关文章
     *
     * @param $data
     *
     * @return array
     */
    public function search_articles($data): array
    {
        $i      = 0;
        $result = [];
        $res    =
            $this->db
                ->select('title,content,articles_base.id,update_at,articles_base.create_at,url,author_id,name')
                ->from('articles_content')
                ->join('articles_base', 'articles_base.id = articles_content.id', 'inner')
                ->join('users_base', 'users_base.id = articles_base.author_id')
                ->join('avatar_url', 'avatar_url.user_id = articles_base.author_id', 'left')
                ->or_like(['title' => $data['keyword'], 'content' => $data['keyword']])
                ->limit($data['limit'], $data['offset'])
                ->get();

        foreach ($res->result() as $row) {
            // 查询文章的三张预览图
            $articles_image_urls = $this->db
                ->select('url')
                ->from('image_url')
                ->where(['article_id' => $row->id])
                ->get()->result();
            $images              = [];
            $j                   = 0;
            foreach ($articles_image_urls as $url) {
                if ( ! empty($url->url) && $j++ < 3) {
                    $images[] = $url->url;
                }
            }

            // 格式化数据
            $result[$i++] =
                [
                    'title'     => $row->title,
                    'content'   => $row->content,
                    'id'        => (int)$row->id,
                    'update_at' => date('c', strtotime($row->update_at)),
                    'create_at' => date('c', strtotime($row->create_at)),
                    'author'    =>
                        [
                            'id'         => $row->author_id,
                            'name'       => $row->name,
                            'avatar_url' => $row->url
                        ],
                    'images'    => $images
                ];
        }

        $total = $res->num_rows();

        return ['articles' => $result, 'total' => (int)$total];
    }

}