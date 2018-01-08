<?php

class Users_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 用户登录
     *
     * @param array $data
     *
     * @return bool
     */
    public function login(array $data): bool
    {
        $res = $this->db
            ->select('id,name,mail')
            ->from('users_base')
            ->where(['mail' => $data['mail'], 'password' => md5($data['password'])])
            ->get();

        if ($res->num_rows()) {
            $info       = $res->result();
            $this->id   = $info[0]->id;
            $this->name = $info[0]->name;
            $this->mail = $info[0]->mail;

            return true;
        } else {
            return false;
        }

    }

    /**
     * 用户注册
     *
     * @param array $data
     *
     * @return string
     */
    public function register(array $data): string
    {
        if ($this->emailExists($data['mail'])) {
            return '该邮箱已被注册';
        }
        if ($this->usernameExists($data['name'])) {
            return '用户名已被占用';
        }

        $this->db->trans_start();
        $this->db->insert(
            'users_base',
            [
                'mail'     => $data['mail'],
                'name'     => $data['name'],
                'password' => md5($data['password'])
            ]);
        $this->db->insert(
            'avatar_url',
            [
                'url' => 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
            ]);
        $this->db->insert(
            'users_detail',
            [
                'id'       => $this->db->insert_id(),
                'sex'      => 3,
                'birthday' => '1970-01-01'
            ]);

        $this->mail     = $data['mail'];
        $this->name     = $data['name'];
        $this->password = $data['password'];
        $this->id       = $this->db->insert_id();

        @$this->db->trans_complete();

        if ($this->db->trans_status()) {
            return '';
        } else {
            return '注册失败';
        }
    }

    /**
     * 获取用户信息
     *
     * @param int $id
     *
     * @return array
     */
    public function getUserInfo(int $id): array
    {
        $res = $this->db
            ->select('users_base.id,url,mail,name,sex,birthday')
            ->from('users_base')
            ->join('avatar_url', 'users_base.id = avatar_url.user_id', 'left')
            ->join('users_detail', 'users_base.id = users_detail.id', 'left')
            ->where(['users_base.id' => $id, 'url' => 0])
            ->get();

        $result = $res->result()[0];

        return
            [
                'id'         => $result->id,
                'avatar_url' => $result->url,
                'mail'       => $result->mail,
                'name'       => $result->name,
                'sex'        => $result->sex,
                'birthday'   => $result->birthday
            ];
    }

    /**
     * 修改用户信息
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function modifyInfo(int $id, array $data): bool
    {
        $this->db->trans_start();
        foreach ($data as $item => $value) {
            if ($value !== null) {
                $method = 'modify' . ucfirst($item);
                $res = $this->$method($id,$value);
                if ( ! $res) {
                    return false;
                }
            }
        }
        @$this->db->trans_complete();;

        return $this->db->trans_status();
    }

    /**
     * 修改用户密码
     *
     * @param int   $id
     * @param array $data
     *
     * @return string
     */
    public function modifyPassword(int $id, array $data): string
    {
        $res = $this->db
            ->select('password')
            ->from('users_base')
            ->where(['id' => $id])
            ->get();
        if ($res->result()[0]->password !== md5($data['old_psd'])) {
            return '密码验证失败！';
        }

        $res = $this->db
            ->where(['id' => $id])
            ->update('users_base', ['password' => md5($data['new_psd'])]);
        if ($res) {
            return '';
        } else {
            return '修改密码失败';
        }

    }

    /**
     * 判断email是否注册
     *
     * @param string $email
     *
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $res = $this->db
            ->select('id')
            ->from('users_base')
            ->where(['mail' => $email])
            ->get();

        return (bool)$res->num_rows();
    }

    /**
     * 判断用户名是否存在
     *
     * @param string $name
     *
     * @return bool
     */
    public function usernameExists(string $name): bool
    {
        $res = $this->db
            ->select('id')
            ->from('users_base')
            ->where(['name' => $name])
            ->get();

        return (bool)$res->num_rows();
    }

    /**
     * 修改用户名
     *
     * @param $id
     * @param $name
     *
     * @return bool
     */
    public function modifyName($id, $name): bool
    {
        if ($this->usernameExists($name)) {
            return false;
        }
        $this->db->where(['id' => $id])
                 ->update(
                     'users_base',
                     [
                         'name' => $name
                     ]);

        return true;
    }

    /**
     * 修改性别
     *
     * @param $id
     * @param $sex
     *
     * @return bool
     */
    private function modifySex($id, $sex): bool
    {
        $get_sex_code =
            $this->db
                ->select('id')
                ->from('sex_detail')
                ->where(['sex' => $sex])
                ->get();
        if (!$get_sex_code->num_rows()){
            return false;
        }
        $sex_code = $get_sex_code->result()[0]->id;
        $this->db->where(['id' => $id])
                 ->update(
                     'users_detail',
                     ['sex' => $sex_code]);

        return true;
    }

    /**
     * 修改生日
     *
     * @param $id
     * @param $birthday
     *
     * @return bool
     */
    private function modifyBirthday($id, $birthday): bool
    {
        $this->db->where(['id' => $id])
                 ->update(
                     'users_detail',
                     [
                         'birthday' => $birthday
                     ]);

        return true;
    }

    /**
     * 修改用户头像
     *
     * @param $id
     * @param $url
     *
     * @return bool
     */
    public function modifyUrl($id, $url): bool
    {
        $this->db->where(['user_id' => $id])
                 ->update(
                     'avatar_url',
                     [
                         'url' => $url
                     ]);

        return true;
    }





//
///**
// * 修改用户信息
// *
// * @param int   $id
// * @param array $data
// *
// * @return bool
// */
//public function modifyInfo(int $id, array $data): bool
//{
//    $this->db->trans_start();
//
//    // 修改用户昵称
//    if ( ! empty($data['name'])) {
//        if ($this->usernameExists($data['name'])) {
//            return '用户名已存在';
//        }
//        $this->db->where(['id' => $id])
//                 ->update(
//                     'users_base',
//                     [
//                         'name' => $data['name']
//                     ]);
//    }
//    // 修改用户性别
//    if ( ! empty($data['sex'])) {
//        $this->db->where(['id' => $id])
//                 ->update(
//                     'users_detail',
//                     ['sex' => $data['sex']]);
//    }
//    // 修改用户生日
//    if ( ! empty($data['birthday'])) {
//        $this->db->where(['id' => $id])
//                 ->update(
//                     'users_detail',
//                     [
//                         'birthday' => $data['birthday']
//                     ]);
//    }
//    // 修改用户头像
//    if ( ! empty($data['avatar_url'])) {
//        $this->db->where(['user_id' => $id])
//                 ->update(
//                     'avatar_url',
//                     [
//                         'url' => $data['avatar_url']
//                     ]);
//    }
//
//    @$this->db->trans_complate();
//
//    return $this->db->trans_status();
//}
/**
     *  A13 获取用户收藏列表
     * @param $data
     * @param bool $paging
     * @return int|mixed
     */
    public function get_collect_articles($data){

        //articles_status.status       文章状态（是否被删除）
        //user_collections.article_id  文章id
        //articles_content.title       文章标题
        //articles_content.content     文章内容
        //ser_collections.create_at    收藏时间
        //
        $select = ' user_collections.create_at,
                    user_collections.article_id,
                    articles_content.title,
                    articles_content.content,
                    articles_status.status,
                    
                    ';
        $this->db->select($select);
        $this->db->from('user_collections');
        $this->db->join('articles_content','articles_content.id = user_collections.article_id');
        $this->db->join('articles_status','articles_status.id = user_collections.article_id');
        $this->db->where("user_collections.user_id = {$data['user_id']}");
        $re['article'] =  $this->db->get()->result_array();

        //获取用户收藏总数
        $this->db->select('users_collections_count.count');
        $this->db->from('users_collections_count');
        $this->db->where("user_id = {$data['user_id']}");
        $re['total'] = $this->db->get()->row()->count;
        //$this->db->select();
        //$re['title'] = 
        return $re;

        // $select = 'pb.title,pc.content,uc.create_time,gb.`name`,pb.`delete`,gb.`delete` AS g_delete,uc.post_base_id,pb.group_base_id';
        // $this->db->select($select);
        // $this->db->join('post_base AS pb','uc.post_base_id = pb.id');
        // $this->db->join('post_content AS pc','pc.post_base_id = uc.post_base_id');
        // $this->db->join('group_base AS gb','pb.group_base_id = gb.id');
        // $this->db->order_by('uc.create_time ','DESC');
        // $this->db->where("uc.user_base_id = {$data['user_id']} ");
        // $this->db->where('uc.delete = 0');
        // if($paging){
        //     return $this->db->count_all_results('user_collection AS uc');
        // }else{
        //     return $this->db->get('user_collection AS uc',$data['limit'],$data['offset'])->result_array();
        // }
    }


    /**
     * 用文章id搜索文章对应的图片
     * @param  [array] $data [description]
     * @return [array]       [description]
     */
    public function get_article_img($data)
    {
        $this->db->select('image_url.url');
        $this->db->from('image_url');
        $this->db->where("image_url.article_id = {$data['article_id']}");
        $this->db->where("image_url.delete_flg = 0");
        $re= $this->db->get()->result_array();
        return $re;

    }
}