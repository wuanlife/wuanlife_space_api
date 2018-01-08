<?php
/**
 * Created by PhpStorm.
 * User: lance
 * Date: 17-12-31
 * Time: 下午5:25
 */

/**
 * 管理员模型
 * Class AdminsModel
 */
class Admins_model extends CI_Model
{

    /**
     * 通过用户名判断用户是否存在
     * @param string $s_username 用户名
     * @return object|false
     */
    public function userIsExists(string $s_username)
    {
        return $this->db->get_where('users_base', ['name' => $s_username], 1)->result_array()[0] ?? false;
    }
    /**
     * 通过id判断用户是否存在
     * @param int id
     * @return object|false
     */
    public function userIsById(int $i_id)
    {
        return $this->db->get_where('users_base', ['id' => $i_id], 1)->result_array()[0] ?? false;
    }
    /**
     * 添加管理员
     * @param array $a_adminData
     * @return boolean
     */
    public function addAdmin(array $a_adminData)
    {
        $this->db->insert('users_base', $a_adminData);
        return $this->db->affected_rows() ? true : false;
    }

    /**
     * 删除管理员与对应权限
     * @param int $id
     * @return bool
     */
    public function deleteAdmin(int $id)
    {
        $this->db->delete('users_base', ['id' => $id]);
        if ($this->db->affected_rows()):
            //删除对应权限
            $this->db->delete('users_auth',['id' => $id]);
            return true;
        else:
            return false;
        endif;
    }
}
