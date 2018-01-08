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

        $res = $this->db->insert(
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

        $this->mail     = $data['mail'];
        $this->name     = $data['name'];
        $this->password = $data['password'];
        $this->id       = $this->db->insert_id();

        if ($res) {
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
            ->select('id,url,mail,name')
            ->from('users_base')
            ->join('avatar_url', 'users_base.id = avatar_url.user_id', 'left')
            ->where(['id' => $id, 'url' => 0])
            ->get();

        $result = $res->result()[0];

        return
            [
                'id'         => $result->id,
                'avatar_url' => $result->url,
                'mail'       => $result->mail,
                'name'       => $result->name
            ];
    }

    /**
     * 修改用户信息
     *
     * @param int   $id
     * @param array $data
     *
     * @return string
     */
    public function modifyInfo(int $id, array $data): string
    {
        $res = '缺少需要修改的参数';

        if ( ! empty($data['name'])) {
            if ($this->usernameExists($data['name'])) {
                return '用户名已存在';
            }
            $res1 = $this->db
                ->where(['id' => $id])
                ->update('users_base', ['name' => $data['name']]);
            $res  = $res1 ? '' : '用户名修改失败';
        }
        if ( ! empty($data['avatar_url'])) {
            $res2 = $this->db
                ->where(['user_id' => $id])
                ->update('avatar_url', ['url' => $data['avatar_url']]);
            $res  .= $res2 ? '' : ',用户头像修改失败';
        }

        return $res;
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

}