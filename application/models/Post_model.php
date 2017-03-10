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
    public function get_group_id($post_id){
        return $this->get_post_information($post_id)['group_base_id'];
    }
    public function get_post_base($post_id,$user_id){
        if(empty($user_id)){
            $user_id = 0;
        }
        $sql = 'SELECT pb.id AS post_id,gb.id AS group_id,gb.name AS g_name,pb.title AS p_title,pd.text AS p_text,'
            .'ub.id AS user_id,ub.nickname AS user_name,'
            .'pd.create_time,pb.sticky,pb.`lock`,'
            ."(SELECT approved FROM post_approved WHERE user_base_id=$user_id AND post_base_id=$post_id AND floor=1) AS approved,"
            ."(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=$post_id AND approved=1) AS approvednum "
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
            . 'WHERE pb.id=pd.post_base_id AND pb.`delete`=0 AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
            ."AND pb.id=$post_id AND pd.floor=1" ;
        $rs = $this->db->query($sql)->result_array();
        foreach ($rs as $key => $value) {
            if(empty($rs["$key"]['approved'])){
                $rs["$key"]['approved'] = '0';
            }
        }
        if (!empty($rs)){
            $rs[0]['sticky']=(int)$rs[0]['sticky'];
            $rs[0]['lock']=(int)$rs[0]['lock'];
            preg_match_all("(http://[-a-zA-Z0-9@:%_\+.~#?&//=]+[.jpg.gif.png])",$rs[0]['p_text'],$rs[0]['p_image']);
        }
        return $rs;
    }
    public function get_post_reply($post_id,$pn,$user_id){
        $num=30;                    //每页显示数量
        $rs   = array();

        $rs['postID']=$post_id;
        $sql = "SELECT ceil(count(pd.post_base_id)/$num) AS page_count,count(*) AS reply_count "
            . 'FROM post_detail as pd '
            . "WHERE pd.post_base_id=$post_id AND pd.floor>1 AND pd.delete=0 ";
        $count = $this->db->query($sql)->result_array();
        $rs['reply_count'] = (int)$count[0]['reply_count'];
        $rs['page_count'] = (int)$count[0]['page_count'];
        if ($rs['page_count'] == 0 ){
            $rs['page_count']=1;
        }
        if($pn > $rs['page_count']){
            $pn = $rs['page_count'];
        }
        $limsit_st = ($pn-1)*$num;
        $rs['current_page'] = $pn;
        $sql = 'SELECT pd.reply_floor,pd.text AS p_text,ub.id AS user_id,ub.nickname AS user_name,pd.reply_id,'
            .'(SELECT nickname FROM user_base WHERE user_base.id = pd.reply_id) AS reply_nick_name,'
            ."pd.create_time,pd.floor AS p_floor,(SELECT approved FROM post_approved WHERE user_base_id=$user_id AND post_base_id=$post_id AND floor=pd.floor) AS approved,"
            ."(SELECT count(approved) FROM post_approved WHERE floor=pd.floor AND post_base_id=$post_id AND approved=1) AS approvednum "
            . 'FROM user_base ub,post_detail pd '
            . "WHERE pd.post_base_id = $post_id AND pd.`delete` = 0 AND pd.floor > 1 AND ub.id=pd.user_base_id "
            . 'ORDER BY pd.floor ASC '
            . "LIMIT $limsit_st,$num ";
        $rs['reply'] = $count = $this->db->query($sql)->result_array();
        foreach ($rs['reply'] as $key => $value) {
            if(empty($rs['reply']["$key"]['approved'])){
                $rs['reply']["$key"]['approved'] = '0';
            }
        }
        return $rs;
    }
    public function get_post_information($post_id){
        $this->db->select('*');
        $this->db->from('post_base');
        $this->db->where('id',$post_id);
        $this->db->join('post_detail', 'post_detail.post_base_id = post_base.id');
        $this->db->where('floor',1);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function judge_collect_post($post_id,$user_id){
        $sql=$this->db->select('*')
            ->from('user_collection')
            ->where('post_base_id',$post_id)
            ->where('user_base_id',$user_id)
            ->where('`delete`',0)
            ->get()
            ->row_array();
        if($sql){
            $collect=1;
        }else{
            $collect=0;
        }
        return $collect;
    }

    /**
     * @param $p_id
     * @param $floor
     * @return bool|int
     * 查询帖子回复所在的页数
     */
    public function get_post_reply_page($p_id,$floor){
        $num=30;
        $sql = "SELECT ceil(count(pd.post_base_id)/$num) AS page_count,count(*) AS reply_count "
            . 'FROM post_detail as pd '
            . "WHERE pd.post_base_id=$p_id AND pd.floor>1 AND pd.delete=0 ";
        $count = $this->db->query($sql)->result_array();
        for($i=1;$i<=$count[0]['page_count'];$i++){
            $floors = $this->db->from('post_detail')
                ->SELECT('floor')
                ->WHERE('post_base_id',$p_id)
                ->WHERE('floor >',1)
                ->WHERE('delete',0)
                ->limit(($i-1)*$num,$num)
                ->get()
                ->result_array();
            foreach($floors as $key =>$value){
                if($value['floor'] == $floor){
                    return $i;
                }
            }
        }
        if($num>$floor){
            return 1;
        }
        return false;
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

    /**
     * @param $data
     * @return array
     * 帖子回复
     */
    public function post_reply($data) {
        $time = date('Y-m-d H:i:s',time());
        //查询最大楼层
        $sql=$this->db->from('post_detail')
            ->select('post_base_id,user_base_id,max(floor)')
            ->where('post_base_id',$data['post_base_id'])
            ->get()
            ->row_array();
        $data['create_time'] = $time;
        $data['floor'] = ($sql['max(floor)'])+1;
        $reply_id=$this->db->from('post_detail')
            ->select('user_base_id')
            ->where('post_base_id',$data['post_base_id'])
            ->where('floor',$data['reply_floor'])
            ->get()
            ->row_array();
        $data['reply_id']=$reply_id['user_base_id'];
        $rs = $this->db->insert('post_detail',$data);
        if($rs){
            return $data;
        }else{
            return false;
        }
    }
    public function edit_post($data){
        $b_data = array(
            'title' => $data['title'],
        );
        $d_data = array(
            'text' => $data['text'],
            'create_time' => time(),
        );
        $this->db->where('id', $data['post_base_id'])
            ->update('post_base',$b_data);
        $this->db->where('post_base_id', $data['post_base_id'])
            ->where('floor',1)
            ->update('post_detail',$d_data);
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