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












}