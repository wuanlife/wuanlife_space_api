<?php



class Group_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    /**
     * 创建星球
     * @param $data
     * @return array
     */
    public function create($data){
        $base=array(
            'name'=>$data['g_name'],
            'g_introduction'=>$data['g_introduction'],
            'g_image'=>$data['g_image'],
            'private'=>$data['private']
        );
        $this->db->insert('group_base',$base);
        $group_id = $this->db->insert_id();
        $detail=array(
            'group_base_id'=>$group_id,
            'user_base_id'=>$data['user_id'],
            'authorization'=>'01',
        );
        $this->db->insert('group_detail',$detail);
        return ['id'=>$group_id];
    }

    /**
     * 通过星球id获取星球详情
     * @param $group_id
     * @return mixed
     */
    public function get_group_infomation($group_id){
        $this->db->select('*');
        $this->db->from('group_base');
        $this->db->where('id',$group_id);
        $this->db->join('group_detail', 'group_detail.group_base_id = group_base.id');
        $this->db->where('authorization',01);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * 根据星球名判断星球是否存在
     * @param $g_name
     * @return mixed
     */
    public function gname_exist($g_name){
        $re=$this->db->select('id')
            ->where('name = ',$g_name)
            ->get('group_base')
            ->row_array();
        return $re['id'];
    }

    /**
     * 获得星球的帖子总数，旧式方法 *2017/7/24 0024
     * @param $group_id
     * @return int
     */
    public function get_group_post_num($group_id)
    {
        $re=$this->db->select('id')
            ->where('group_base_id',$group_id)
            ->where('delete',0)
            ->get('post_base')
            ->result_array();
        return count($re);
    }

    /**
     * 获取星球的成员数
     * @param $group_id
     * @return int
     */
    public function get_group_user_num($group_id)
    {
        $re=$this->db->select('user_base_id')
            ->where('group_base_id',$group_id)
            ->get('group_detail')
            ->result_array();
        return count($re);
    }

    /**
     * 将退出星球的消息发送给星球主人
     * @param $data
     */
    public function quit_message($data){
        $creator = $this->get_group_infomation($data['group_id'])['user_base_id'];
        $field = array(
            'user_base_id'      =>$creator,
            'user_notice_id'     =>$data['user_id'],
            'group_base_id'     =>$data['group_id'],
            'create_time'        =>time(),
            'type'              =>4,
            'status'            =>0
        );
        $this->db->insert('message_notice',$field);
    }

    /**
     * 退出星球
     * @param $data
     */
    public function quit($data){
        $this->db->where('group_base_id',$data['group_id'])
            ->where('user_base_id',$data['user_id'])
            ->where('authorization','03')
        ->delete('group_detail');
    }

    /**
     * 修改星球信息
     * @param $data
     * @return bool
     */
    public function alter_group_info($data){
        $re=$this->db->set('g_introduction',$data['g_introduction'])
            ->set('g_image',$data['g_image'])
            ->set('private',$data['private'])
            ->where('id',$data['group_id'])
            ->update('group_base');
        return $re;
    }

    /**
     * 将加入星球的消息发送给星球主人
     * @param $data
     */
    public function join_message($data){
        $creator = $this->get_group_infomation($data['group_base_id'])['user_base_id'];
        $field = array(
            'user_base_id'      =>$creator,
            'user_notice_id'     =>$data['user_base_id'],
            'group_base_id'     =>$data['group_base_id'],
            'create_time'        =>time(),
            'type'              =>5,
            'status'            =>0
        );
        $this->db->insert('message_notice',$field);
    }

    /**
     * 加入星球
     * @param $data
     * @return bool
     */
    public function join_group($data){
        $re=$this->db->insert('group_detail', $data);
        return $re;
    }

    /**
     * 判断用户是否已加入星球，存在相同方法，后续会移除*2017/7/25 0025
     * @param $user_id
     * @param $group_id
     * @return bool
     */
    /**
     * public function check_group($user_id,$group_id){
        $re =  $this->db->where('group_base_id',$group_id)
            ->where('user_base_id',$user_id)
            ->where('authorization','03')
            ->from('group_detail')
            ->get()
            ->row_array();
        if(!empty($re)){
            return true;
        }else{
            return false;
        }
    }*/

    /**
     * 获取星球列表
     * @param $offset
     * @param $limit
     * @param null $name
     * @param null $user_id
     * @return array
     */
    public function lists($offset,$limit,$name = NULL,$user_id = NULL){
        if($name){
            $name = strtolower($name);
            $where = [
                'delete' =>0,
                'lower(name) LIKE'   =>"%{$name}%"
            ];
        }elseif($user_id){
            $where = [
                'delete' =>0,
                'user_base_id'   =>$user_id
            ];
        }else{
            $where = [
                'delete' =>0
            ];
        }
        $this->db->select('name,g_image AS image_url,g_introduction AS introduction,id,COUNT(group_detail.user_base_id) AS member_num');
        $this->db->join('group_detail', 'group_detail.group_base_id = group_base.id');
        $this->db->group_by("group_base.id");
        $this->db->order_by('member_num ','DESC');
        $this->db->having('member_num >= 1');
        return $this->db->get_where('group_base', $where, $limit, $offset)->result_array();
    }

    /**
     * 获取星球数量
     * @param null $name
     * @param null $user_id
     * @return int
     */
    public function get_group_num($name = NULL,$user_id = NULL)
    {
        if ($name) {
            $name = strtolower($name);
            $where = [
                'delete' => 0,
                'lower(name) LIKE' => "%{$name}%"
            ];
        }elseif($user_id){
            $where = [
                'delete' =>0,
                'user_base_id'   =>$user_id
            ];
        }else{
            $where = [
                'delete' =>0
            ];
        }
        $this->db->where($where);
        $this->db->join('group_detail', 'group_detail.group_base_id = group_base.id');
        $this->db->from('group_base');
        $this->db->group_by("group_base.id");
        return $this->db->count_all_results();
    }

    /**
     * 将申请加入私密星球的消息发送给星球主人
     * @param $data
     * @param $creator_id
     * @return bool
     */
    public function private_group($data,$creator_id){
        $field = array(
            'user_base_id'      =>$creator_id,
            'user_apply_id'     =>$data['user_id'],
            'group_base_id'     =>$data['group_id'],
            'create_time'        =>time(),
            'text'              =>$data['text'],
            'status'            =>0
        );
        $boolean =  $this->db->insert('message_apply',$field);
        if($boolean){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 获取星球成员
     * @param $data
     * @param bool $num
     * @return array|int
     */
    public function group_member($data,$num = FALSE){
        $where = [
            'group_base_id'=>$data['group_id'],
            'authorization'=>'03'
        ];
        if($num){
            $this->db->where($where);
            $this->db->from('group_detail');
            return $this->db->count_all_results();
        }else{
            return $this->db->get_where('group_detail', $where, $data['limit'], $data['offset'])
                ->result_array();
        }
    }

    /**
     * 删除星球成员
     * @param $data
     * @return bool
     */
    public function delete_group_member($data){
        $boolean = $this->Common_model->judge_group_user($data['group_id'],$data['member_id']);
        if($boolean == NULL){
            return false;
        }else{
            $this->db->where('group_base_id',$data['group_id'])
                ->where('user_base_id',$data['member_id'])
                ->where('authorization','03')
                ->delete('group_detail');
            return true;
        }
    }

    /**
     * 删除星球成员消息反馈给成员
     * @param $data
     * @return bool
     */
    public function dgm_message($data){
        $field=array(
            'user_base_id'      =>$data['member_id'],
            'user_notice_id'    =>$data['user_id'],
            'group_base_id'     =>$data['group_id'],
            'create_time'       =>time(),
            'status'            =>0,
            'type'              =>3
        );
        return $this->db->insert('message_notice',$field);
    }




}
