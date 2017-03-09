<?php



class Post_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    public function search_posts($text,$pnum,$pn){
        if(empty($pn)){
            $rs = array();
            return $rs;
        }
        $text = strtolower($text);
        $num=($pn-1)*$pnum;
        $sql = 'SELECT pb.id AS postID,pb.title,pd.text,pb.lock,pd.create_time,ub.nickname,gb.id AS groupID,gb.name AS groupName '
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
            . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete='0' AND gb.delete='0' AND gb.private='0' "
            . "AND lower(pb.title) LIKE '%$text%' "
            . 'GROUP BY pb.id '
            . 'ORDER BY COUNT(pd.post_base_id) DESC '
            . "LIMIT $num,$pnum";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function search_posts_num($text){
        $text = strtolower($text);
        $sql = 'SELECT count(*) AS num '
            . "FROM post_base pb,group_base gb WHERE pb.delete=0 AND pb.group_base_id=gb.id AND gb.private='0' AND gb.delete='0'"
            . "AND lower(pb.title) LIKE '%$text%'";
        $query = $this->db->query($sql);
        return $query->row_array()['num'];
    }


    public function get_index_post($data){
        $num=30;
        $user_id=$data['user_id'];
        $rs=array();
        $sql = "SELECT ceil(count(*)/$num) AS pageCount "
            . "FROM post_base pb,group_base gb WHERE pb.delete=0 AND pb.group_base_id=gb.id AND gb.private='0' AND gb.delete='0'";
        $pageCount=$this->db->query($sql)->result_array()[0];
        $rs['pageCount'] = (int)$pageCount['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        if($data['page'] > $rs['pageCount']){
            $data['page'] = $rs['pageCount'];
        }
        $start=($data['page']-1)*$num;
        $rs['currentPage'] = (int)$data['page'];
        $sql = "SELECT pb.id AS postID,pb.title,pd.text,pb.lock,pd.create_time,ub.nickname,gb.id AS groupID,gb.name AS groupName,(SELECT approved FROM post_approved WHERE user_base_id=$user_id AND post_base_id=pb.id AND floor=1) AS approved,(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=pb.id AND approved=1) AS approvednum "
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub,post_approved pa '
            . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete='0' AND gb.delete='0' AND gb.private='0' "
            . 'GROUP BY pb.id '
            . 'ORDER BY MIN(pd.create_time) DESC '
            . "LIMIT $start,$num ";
        $this->db->flush_cache();
        $rs['posts']=$this->db->query($sql)->result_array();
        foreach ($rs['posts'] as $key => $value) {
            if(empty($rs['posts']["$key"]['approved'])){
                $rs['posts']["$key"]['approved'] = '0';
            }
        }
        return $rs;
    }

    public function get_image_url($data){
        $rs = $data;
        for ($i=0; $i<count($rs['posts']); $i++) {
            $rs['posts'][$i]['text'] = str_replace('\"', '', $rs['posts'][$i]['text']);
            preg_match_all('/<img[^>]*src\s?=\s?[\'|"]([^\'|"]*)[\'|"]/is', $rs['posts'][$i]['text'], $picarr);
            $rs['posts'][$i]['image']=$picarr['1'];
        }
        return $rs;
    }

    /*
    * 过滤帖子列表image中gif格式的url
     */
    public function delete_image_gif($data){
        $rs = $data;
        $datab = "/([http|https]):\/\/.*?\.gif/";
        foreach ($rs['posts'] as $key1 => $value) {
            if(!empty($value['image'])){
                foreach ($value['image'] as $key2 => $image) {
                    if(preg_match($datab, $image)){
                        unset($rs['posts'][$key1]['image'][$key2]);
                    }
                }
            }
        }
        return $rs;
    }

    /*
     * 设置帖子列表image图片url上限
     */
    public function post_image_limit($data){
        $rs=$data;
        foreach ($rs['posts'] as $key => $value) {
            if(count($value['image'])>3){
                $rs['posts'][$key]['image'] = array_slice($value['image'],0,3);
            }
        }
        return $rs;
    }

    /*
     * 删除帖子列表html
     */
    public function delete_html_posts($data){
        $rs = $data;
        for ($i=0; $i<count($rs['posts']); $i++) {
            $rs['posts'][$i]['text'] = strip_tags($rs['posts'][$i]['text']);

        }
        return $rs;
    }

    /*
    帖子预览文本限制100
     */
    public function post_text_limit($data){
        $rs=$data;
        for ($i=0; $i<count($rs['posts']); $i++) {
            $rs['posts'][$i]['text'] =mb_convert_encoding(substr($rs['posts'][$i]['text'],0,299), 'UTF-8','GB2312,UTF-8');
        }
        return $rs;
    }


    /*
     * 我的星球帖子展示
     */
    public function get_mygroup_post($user_id,$page) {
        $num=30;
        $rs   = array();

        $sql = "SELECT ceil(count(*)/$num) AS pageCount "
            . 'FROM post_base pb,group_base gb,group_detail gd '
            . "WHERE pb.group_base_id=gb.id AND gb.id=gd.group_base_id AND gd.user_base_id=$user_id AND pb.delete=0 AND gb.delete=0 ";
        $pageCount=$this->db->query($sql)->result_array()[0];

        $rs['pageCount'] = (int)$pageCount['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        if($page > $rs['pageCount']){
            $page = $rs['pageCount'];
        }
        $start=($page-1)*$num;
        $rs['currentPage'] = (int)$page;
        $sql = 'SELECT  pb.id AS postID,pb.title,pd.text,pb.lock,pd.create_time,ub.nickname,gb.id AS groupID,gb.name AS groupName '
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
            . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete=0 AND gb.delete=0 '
            . "AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id =$user_id )"
            . 'GROUP BY pb.id '
            . 'ORDER BY MAX(pd.create_time) DESC '
            . "LIMIT $start,$num ";
        $this->db->flush_cache();
        $rs['posts']=$this->db->query($sql)->result_array();
        return $rs;
    }


    /*
     * 通过用户id获得用户昵称
     */
    public function get_user($user_id){
        $re=$this->db->select('nickname')
            ->where('id',$user_id)
            ->get('user_base')
            ->row_array();
        return $re['nickname'];
    }



    /*
     * 通过星球id查找创建者id
     */
    public function get_creater_id($group_id){
        $re=$this->db->select('user_base_id')
            ->where('group_base_id',$group_id)
            ->where('authorization','01')
            ->get('group_detail')
            ->row_array();
        return $re;
    }

    /*
     * 通过星球id返回星球创建者昵称
     */
    public function get_creator($group_id){
        $re=$this->db->select('user_base_id')
            ->where('group_base_id',$group_id)
            ->where('authorization','01')
            ->get('group_detail')
            ->row_array();
        $user_base_id=$re['user_base_id'];
        $this->db->flush_cache();
        $re=$this->db->select('nickname')
            ->where('id',$user_base_id)
            ->get('user_base')
            ->row_array();
        return $re['nickname'];
    }


    public function get_group_post($group_id,$page){

        $num=30;
        $rs   = array();

        $groupData=$this->db->select('id as groupID,name as groupName')
            ->where('id',$group_id)
            ->get('group_base')
            ->row_array();


        if(empty($groupData)){
            return null;
        }
        $rs['groupID'] = $groupData['groupID'];
        $rs['groupName'] = $groupData['groupName'];


        $sql = "SELECT ceil(count(*)/$num) AS pageCount "
            . 'FROM post_base pb,group_base gb '
            . "WHERE pb.group_base_id=gb.id AND gb.id=$group_id AND pb.delete=0 ";
        $pageCount = $this->db->query($sql)->result_array();


        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        if($page > $rs['pageCount']){
            $page = $rs['pageCount'];
        }
        $rs['currentPage'] = $page;
        $start=($page-1)*$num;
        $sql = 'SELECT  pb.digest,pb.id AS postID,pb.title,pd.text,pd.create_time,ub.id,ub.nickname,pb.sticky,pb.lock '
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
            . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id=$group_id AND pb.delete=0 "
            . 'GROUP BY pb.id '
            . 'ORDER BY pb.sticky DESC, '
            . 'MAX(pd.create_time) DESC '
            . "LIMIT $start,$num ";
        $this->db->flush_cache();

        $rs['posts'] = $this->db->query($sql)->result_array();

        return $rs;
}


}