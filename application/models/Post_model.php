<?php



class Post_model extends CI_Model
{
    /**
     * 构造函数，提前执行
     * Post_model constructor.
     */
    public function __construct()
    {
        $this->load->database();
    }

    /**
     * 获取主页
     * @param $data
     * @param bool $paging
     * @return array|int
     */
    public function get_post($data,$paging = FALSE){  //  2017/7/24 0024  拆分帖子评论表之后的写法
        if($data['name']){
            $data['name'] = strtolower($data['name']);
            $where = [
                'pb.delete' =>0,
                'lower(pb.title) LIKE'   =>"%{$data['name']}%"
            ];
        }else{
            $where = [
                'pb.delete' =>0
            ];
        }
        $select = "(SELECT count(approved) FROM post_approved WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor=1 AND approved=1) AS approved,"
                .'(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=pb.id AND approved=1) AS approved_num,'
                ."(SELECT count(user_base_id) FROM user_collection WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND `delete`=0) AS collected,"
                ."(SELECT count(user_base_id) FROM user_collection WHERE post_base_id=pb.id AND `delete`=0) AS collected_num,"
                ."(SELECT count(user_base_id) FROM post_comment WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied,"
                .'(SELECT count(user_base_id) FROM post_comment WHERE post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied_num ';
        $this->db->select($select);
        $this->db->select('pb.id,pb.title,pc.content,pb.lock,pc.create_time,pb.user_base_id,pb.group_base_id,ud.profile_picture,ub.nickname,gb.name');
        $this->db->join('post_content AS pc','pc.post_base_id = pb.id AND pc.post_base_id = pb.id');
        $this->db->join('user_base AS ub','ub.id = pb.user_base_id AND ub.id = pb.user_base_id');
        $this->db->join('user_detail AS ud','ud.user_base_id = pb.user_base_id AND ud.user_base_id = pb.user_base_id');
        $this->db->join('group_base AS gb','gb.id = pb.group_base_id AND gb.id = pb.group_base_id AND gb.`delete` = 0');
        $this->db->order_by('pc.create_time ','DESC');
        if(!empty($data['user_id'])){
            if($data['latest']===FALSE)
            $this->db->join('group_detail AS gd',"gd.group_base_id = pb.group_base_id AND gd.user_base_id = {$data['user_id']}");
        }
        $this->db->where($where);
        if($paging){
            return $this->db->count_all_results('post_base AS pb');
        }else{
            return $this->db->get('post_base AS pb',$data['limit'],$data['offset'])->result_array();
        }
    }

    /**
     * 获取主页，旧式查询方法 *2017/7/24 0024
     *
     */
    /**
     * public function get_index_post($data,$paging = FALSE){
        if($paging){
            $this->db->where('post_base.delete',0);
            $this->db->join('group_base','group_base.id = post_base.group_base_id AND group_base.`delete` = 0 AND group_base.private = 0');
            return $this->db->count_all_results('post_base');
        }
        $sql = 'SELECT pb.id AS post_id,pb.title as p_title,pd.text as p_text,pb.`lock`,pd.create_time,'
            .'ud.profile_picture,ub.id AS user_id,'
            .'ub.nickname as user_name,gb.id AS group_id,gb.`name` AS g_name,'
            ."(SELECT count(approved) FROM post_approved WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor=1 AND approved=1) AS approved,"
            .'(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=pb.id AND approved=1) AS approved_num,'
            ."(SELECT count(user_base_id) FROM user_collection WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND `delete`=0) AS collected,"
            ."(SELECT count(user_base_id) FROM user_collection WHERE post_base_id=pb.id AND `delete`=0) AS collected_num,"
            ."(SELECT count(user_base_id) FROM post_detail WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied,"
            .'(SELECT count(user_base_id) FROM post_detail WHERE post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied_num '
            .'FROM post_detail pd,post_base pb ,group_base gb,user_base ub,user_detail ud '
            .'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id '
            .'AND ub.id=ud.user_base_id '
            .'AND pb.group_base_id=gb.id AND pb.delete=0 '
            .'AND gb.delete=0 AND gb.private=0 '
            . 'GROUP BY pb.id '
            . 'ORDER BY MIN(pd.create_time) DESC '
            . "LIMIT {$data['offset']},{$data['limit']} ";
        $rs['data']=$this->db->query($sql)->result_array();
//        foreach ($rs['posts'] as $key => $value) {
//            if($value['replied']>0){
//                $rs['posts']["$key"]['replied'] = '1';
//            }
//            if(empty($value['profile_picture'])){
//                $rs['posts']["$key"]['profile_picture'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
//            }
//        }
        return $rs;
    }*/

    /**
     * 获取指定星球的帖子详情
     * @param $data
     * @param bool $paging
     * @return mixed
     */
    public function get_group_post($data,$paging = FALSE){
        $select = "(SELECT count(approved) FROM post_approved WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor=1 AND approved=1) AS approved,"
            .'(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=pb.id AND approved=1) AS approved_num,'
            ."(SELECT count(user_base_id) FROM user_collection WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND `delete`=0) AS collected,"
            ."(SELECT count(user_base_id) FROM user_collection WHERE post_base_id=pb.id AND `delete`=0) AS collected_num,"
            ."(SELECT count(user_base_id) FROM post_comment WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied,"
            .'(SELECT count(user_base_id) FROM post_comment WHERE post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied_num ';
        $this->db->select($select);
        $this->db->select('pb.id,pb.title,pb.digest,pb.sticky,pc.create_time,pc.content,ub.nickname,pb.user_base_id,pb.group_base_id,ud.profile_picture,gb.name');
        $this->db->join('post_content AS pc','pc.post_base_id = pb.id');
        $this->db->join('user_base AS ub','ub.id = pb.user_base_id');
        $this->db->join('user_detail AS ud','ud.user_base_id = pb.user_base_id');
        $this->db->join('group_base AS gb','gb.id = pb.group_base_id AND gb.id = pb.group_base_id AND gb.`delete` = 0');
        $this->db->where('pb.delete',0);
        $this->db->where('pb.group_base_id',$data['group_id']);
        $this->db->order_by('pc.create_time ','DESC');
        if($paging){
            return $this->db->count_all_results('post_base AS pb');
        }else{
            return $this->db->get('post_base AS pb',$data['limit'],$data['offset'])->result_array();
        }


    }

    /**
     * 获取星球帖子，旧式查询方法 *2017/7/24 0024
     */
    /**
     * public function get_group_post($data){
    $sql = 'SELECT  pb.id AS post_id,pb.title AS p_title,pd.text as p_text,pd.create_time,ub.id AS user_id,ub.nickname as user_name,pb.sticky,pb.lock,pb.digest,ud.profile_picture,'
    ."(SELECT count(approved) FROM post_approved WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor=1) AS approved,"
    .'(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=pb.id AND approved=1) AS approved_num,'
    ."(SELECT count(user_base_id) FROM user_collection WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND `delete`=0) AS collected,"
    ."(SELECT count(user_base_id) FROM user_collection WHERE post_base_id=pb.id AND `delete`=0) AS collected_num,"
    ."(SELECT count(user_base_id) FROM post_detail WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied,"
    .'(SELECT count(user_base_id) FROM post_detail WHERE post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied_num '
    . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub,user_detail ud '
    . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id={$data['group_id']} AND pb.delete=0 AND ub.id = ud.user_base_id "
    . 'GROUP BY pb.id '
    . 'ORDER BY pb.sticky DESC, '
    . 'MAX(pd.create_time) DESC '
    . "LIMIT {$data['offset']},{$data['limit']} ";
    $rs['data'] = $this->db->query($sql)->result_array();
    return $rs;

    }
     */

    /**
     * 我的星球帖子展示，旧式查询方法 *2017/7/24 0024
     */
    /**
     * public function get_mygroup_post($data,$paging = FALSE) {
        if($paging){
            $where_in = $this->db->select('group_base_id')->get_where('group_detail',"user_base_id = {$data['user_id']}")->result_array();
            $where_in =  array_column($where_in,'group_base_id');
            $this->db->where('post_base.delete',0);
            $this->db->where_in('group_base_id',$where_in);
            $this->db->join('group_base','group_base.id = post_base.group_base_id AND group_base.`delete` = 0 AND group_base.private = 0');
            return $this->db->count_all_results('post_base');
        }
        $sql = 'SELECT pb.id AS post_id,pb.title as p_title,pd.text as p_text,pb.lock,pd.create_time,'
            . 'ub.nickname as user_name,ub.id AS user_id,gb.id AS group_id,gb.name AS g_name,ud.profile_picture,'
            ."(SELECT count(approved) FROM post_approved WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor=1 AND approved=1) AS approved,"
            .'(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=pb.id AND approved=1) AS approved_num,'
            ."(SELECT count(user_base_id) FROM user_collection WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND `delete`=0) AS collected,"
            ."(SELECT count(user_base_id) FROM user_collection WHERE post_base_id=pb.id AND `delete`=0) AS collected_num,"
            ."(SELECT count(user_base_id) FROM post_detail WHERE user_base_id={$data['user_id']} AND post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied,"
            .'(SELECT count(user_base_id) FROM post_detail WHERE post_base_id=pb.id AND floor>1 AND `delete`=0) AS replied_num '
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub,user_detail ud '
            . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete=0 AND gb.delete=0 '
            . "AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id ={$data['user_id']} ) AND ub.id=ud.user_base_id "
            . 'GROUP BY pb.id '
            . 'ORDER BY MIN(pd.create_time) DESC '
            . "LIMIT {$data['offset']},{$data['limit']} ";
        $rs['data']=$this->db->query($sql)->result_array();
        return $rs;
    }*/

    /**
     * 搜索帖子，旧式查询方法 *2017/7/24 0024
     * @param $text
     * @param $pnum
     * @param $pn
     * @return mixed
     *
     */
    public function search_posts($text,$pnum,$pn){
        $text = strtolower($text);
        $num=($pn-1)*$pnum;
        $sql = 'SELECT pb.id AS post_id,pb.title AS p_title,pd.text AS p_text,pd.create_time,ub.nickname AS user_name,ub.id AS user_id,gb.id AS group_id,gb.name AS g_name '
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
            . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete='0' AND gb.delete='0' AND gb.private='0' "
            . "AND lower(pb.title) LIKE '%$text%' "
            . 'GROUP BY pb.id '
            . 'ORDER BY COUNT(pd.post_base_id) DESC '
            . "LIMIT $num,$pnum";
        $query = $this->db->query($sql)->result_array();
        return $query;
    }

    /**
     * 搜索帖子数量
     * @param $text
     * @return mixed
     *
     */
    public function search_posts_num($text){
        $text = strtolower($text);
        $sql = 'SELECT count(*) AS num '
            . "FROM post_base pb,group_base gb WHERE pb.delete=0 AND pb.group_base_id=gb.id AND gb.private='0' AND gb.delete='0'"
            . "AND lower(pb.title) LIKE '%$text%'";
        $query = $this->db->query($sql);
        return $query->row_array()['num'];
    }

    /**
     * 通过帖子id查找所属星球id 多余方法，后续删除     *2017/7/25 0025
     */
   /**
    * public function get_group_id($post_id){
        return $this->get_post_information1($post_id)['group_base_id'];
    }*/

    /**
     * 单个帖子的内容详情，不包括回复列表
     * @param $data
     * @return int
     */
    public function get_post_base($data){
        //今天先做到这里   2017-07-24 21:27
        //$this->db->
        $select = "(SELECT count(approved) FROM post_approved WHERE user_base_id={$data['user_id']} AND post_base_id={$data['post_id']} AND floor=1) AS approved,"
                ."(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id={$data['post_id']} AND approved=1) AS approved_num,"
                ."(SELECT count(user_base_id) FROM user_collection WHERE user_base_id={$data['user_id']} AND post_base_id={$data['post_id']} AND `delete`=0) AS collected,"
                ."(SELECT count(user_base_id) FROM user_collection WHERE post_base_id={$data['post_id']} AND `delete`=0) AS collected_num,"
                .'pb.id,pb.user_base_id,pb.group_base_id,pb.title,pc.content,pc.create_time,pb.sticky,pb.`lock`,gb.`name`,'
                .'gb.g_image,gb.g_introduction,creator.id AS creator_id,creator.nickname AS creator_name,author.nickname,'
                .'ud.profile_picture,gb.`delete` AS g_delete,gb.private AS g_private,pb.`delete` AS p_delete';
        $this->db->select($select);
        $this->db->join('post_content AS pc','pc.post_base_id = pb.id');
        $this->db->join('group_base AS gb','gb.id = pb.group_base_id');
        $this->db->join('group_detail AS gd','gd.group_base_id = pb.group_base_id AND gd.authorization = 01');
        $this->db->join('user_base AS creator','creator.id = gd.user_base_id');
        $this->db->join('user_base AS author','pb.user_base_id = author.id');
        $this->db->join('user_detail AS ud','ud.user_base_id = pb.user_base_id');
        $this->db->where('pb.id',$data['post_id']);
        return $this->db->get('post_base AS pb')->row_array();

    }

    /**
     * 单个帖子的内容详情，不包括回复列表  旧式查询方法 *2017/7/24 0024
     * @param $post_id
     * @param $user_id
     * @return mixed
     */
    /**
     * public function get_post_base($post_id,$user_id){
        if(empty($user_id)){
            $user_id = 0;
        }
        $sql = 'SELECT gb.id AS group_id,gb.name AS g_name,gb.g_image,gb.g_introduction,pb.id AS post_id,pb.title AS p_title,pd.text AS p_text,'
            .'ub.id AS user_id,ub.nickname AS user_name,ud.profile_picture,'
            .'pd.create_time,pb.sticky,pb.`lock`,'
            ."(SELECT count(approved) FROM post_approved WHERE user_base_id=$user_id AND post_base_id=$post_id AND floor=1) AS approved,"
            ."(SELECT count(approved) FROM post_approved WHERE floor=1 AND post_base_id=$post_id AND approved=1) AS approved_num,"
            ."(SELECT count(user_base_id) FROM user_collection WHERE user_base_id=$user_id AND post_base_id=$post_id AND `delete`=0) AS collected,"
            ."(SELECT count(user_base_id) FROM user_collection WHERE post_base_id=$post_id AND `delete`=0) AS collected_num "
            . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub,user_detail ud '
            . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
            ."AND pb.id=$post_id AND pd.floor=1 AND ub.id=ud.user_base_id" ;
        $rs = $this->db->query($sql)->row_array();
        if (!empty($rs)){
            $rs['sticky']=(int)$rs['sticky'];
            $rs['lock']=(int)$rs['lock'];
            preg_match_all("(http://[-a-zA-Z0-9@:%_\+.~#?&//=]+[.jpg.gif.png])",$rs['p_text'],$rs['p_image']);
        }
        if(empty($rs['profile_picture'])){
            $rs['profile_picture'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
        }
        if(empty($rs['g_image'])){
            $rs['g_image'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
        }
        return $rs;
    }
    */

    /**
     * 获取帖子回复列表
     * @param $data
     * @param bool $paging
     * @return int|array
     */
    public function get_post_comment($data,$paging = FALSE){
        $select = "(SELECT approved FROM post_approved WHERE user_base_id={$data['user_id']} AND post_base_id={$data['post_id']} AND floor=pc.floor) AS approved,"
        ."(SELECT count(approved) FROM post_approved WHERE floor=pc.floor AND post_base_id={$data['post_id']} AND approved=1) AS approved_num,"
        .'pc.user_base_id AS user_id,pc.`comment`,pc.floor,pc.create_time,pc.reply_floor,ub.nickname AS user_name,pc.reply_id AS replies_id,replies.nickname AS replies_name';
        $this->db->select($select);
        $this->db->join('user_base AS ub','pc.user_base_id = ub.id');
        $this->db->join('user_base AS replies','pc.reply_id = replies.id','LEFT');
        $this->db->where("pc.post_base_id = {$data['post_id']} AND pc.reply_floor = {$data['reply_floor']} AND pc.`delete` = 0");
        $this->db->order_by('pc.create_time','ASC');
        if($paging){
            return $this->db->count_all_results('post_comment AS pc');
        }else{
            $rs =  $this->db->get('post_comment AS pc',$data['limit'],$data['offset'])->result_array();
            foreach ($rs as $k => $v){
                $rs[$k]['approved'] = $v['approved']?TRUE:FALSE;        //点赞返回布尔值
                $rs[$k]['comment'] = strip_tags($rs[$k]['comment']);       //删除帖子评论中的html标签
                date_default_timezone_set('UTC');
                $rs[$k]['create_time'] = date('Y-m-d\TH:i:s\Z',$v['create_time']);      //格式化时间戳
            }
            return $rs;
        }

    }

    /**
     * 单个帖子的回复详情，不包括帖子内容  旧式查询方法 *2017/7/25 0025
     * @param $post_id
     * @param $pn
     * @param $user_id
     * @return array
     *
     */
    /**
     * public function get_post_reply($post_id,$pn,$user_id){
        $num=30;                    //每页显示数量
        $rs   = array();
        if(empty($user_id)){
            $user_id = 0;
        }
        $rs['post_id']=$post_id;
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
            .'pd.create_time,pd.floor AS p_floor,'
            ."(SELECT approved FROM post_approved WHERE user_base_id=$user_id AND post_base_id=$post_id AND floor=pd.floor) AS approved,"
            ."(SELECT count(approved) FROM post_approved WHERE floor=pd.floor AND post_base_id=$post_id AND approved=1) AS approved_num "
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
    */

    /**
     * 通过帖子ID获取帖子详情，开发结束后名字去掉末尾的1
     * @param $post_id
     * @return array
     */
    public function get_post_information1($post_id)
    {
        $select = 'pb.`delete` AS p_delete,gb.`delete` AS g_delete,gb.private,pb.id,pb.user_base_id,pb.group_base_id,'
                . 'pb.title,pb.digest,pb.sticky,pb.`lock`,gb.`name`,gb.g_image,gb.g_introduction';
        $this->db->select($select);
        $this->db->join('group_base AS gb','gb.id = pb.group_base_id');
        return $this->db->get_where('post_base AS pb',"pb.id = {$post_id}")->row_array();

    }

    /**
     * 通过帖子id获取帖子详情     旧式查询方法 *2017/7/25 0025
     * @param $post_id
     * @return mixed
     *
     */
    /**
     * public function get_post_information($post_id){
        $this->db->select('*');
        $this->db->from('post_base');
        $this->db->where('id',$post_id);
        $this->db->join('post_detail', 'post_detail.post_base_id = post_base.id');
        $this->db->where('floor',1);
        $query = $this->db->get();
        return $query->row_array();
    }*/

    /**
     * 发表帖子
     * @param $data
     * @return array|bool
     */
    public function posts($data){
        $b_data = array(
            'user_base_id'  => $data['user_id'],
            'group_base_id' => $data['group_id'],
            'title'         => $data['title'],
        );
        if($this->db->insert('post_base',$b_data)){
            $d_data = array(
                'post_base_id' => $this->db->insert_id('post_base'),
                'content' => $data['content'],
                'create_time' => time(),
                'modify_time' =>time()
            );
            $this->db->insert('post_content',$d_data);
            return [
                'id' =>$d_data['post_base_id']
            ];
        }else{
            return FALSE;
        }
    }

    /**
     * 发表帖子     旧式查询方法 *2017/7/25 0025
     * @param $data
     * @return mixed
     */
    /**
     * public function posts($data){
        $b_data = array(
            'user_base_id'  => $data['user_id'],
            'group_base_id' => $data['group_id'],
            'title'         => $data['p_title'],
        );
        $time = date('Y-m-d H:i:s',time());
        $this->db->insert('post_base',$b_data);
        $d_data = array(
            'post_base_id' => $this->db->insert_id('post_base'),
            'user_base_id' => $data['user_id'],
            'text' => $data['p_text'],
            'floor'=> '1',
            'create_time' => $time,
        );
        $this->db->insert('post_detail',$d_data);
        return $d_data['post_base_id'];
    }*/

    /**
     * 回复帖子
     * @param $data
     * @return bool
     */
    public function post_reply($data) {
        //查询最大楼层
        $this->db->select('Max(pc.floor)+1 AS floor');
        $data['floor'] = $this->db->get_where(
            'post_comment AS pc',
            "pc.post_base_id = {$data['post_base_id']}"
        )->row_array()['floor']?:2;

        $rs = $this->db->insert('post_comment',$data);
        if($rs){
            return $data;
        }else{
            return FALSE;
        }
    }

    /**
     * 获得单个帖子的单个回复详情，目前仅配合post_reply使用
     * @param $data
     * @return array
     */
    public function get_reply($data){
        $this->db->select('pc.user_base_id AS user_id,pc.`comment`,pc.floor,pc.create_time,pc.reply_floor,pc.reply_id AS replies_id,replies.nickname AS replies_name,ub.nickname AS user_name');
        $this->db->join('user_base AS ub','pc.user_base_id = ub.id');
        $this->db->join('user_base AS replies','pc.reply_id = replies.id','LEFT');
        return $this->db->get_where(
            'post_comment AS pc',
            "pc.post_base_id = {$data['post_base_id']} AND pc.floor = {$data['floor']}"
        )->row_array();

    }

    /**
     * 帖子回复 旧式查询方法 *2017/7/25 0025
     * @param $data
     * @return array
     *
     */
    /**
     * public function post_reply($data) {
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
    }*/

    /**
     * 编辑帖子
     * @param $data
     * @return bool
     */
    public function edit_post($data){
        $b_data = array(
            'title' => $data['title'],
        );
        $d_data = array(
            'content' => $data['content'],
            'create_time' => time(),
        );
        return $this->db->where('id', $data['post_id'])
            ->update('post_base',$b_data)?
            $this->db->where('post_base_id', $data['post_id'])
            ->update('post_content',$d_data)?
                TRUE:
                FALSE:
            FALSE;
    }

    /**
     * 编辑帖子 旧式查询方法 *2017/7/25 0025
     * @param $data
     */
    /**
     * public function edit_post($data){
    $b_data = array(
    'title' => $data['title'],
    );
    $d_data = array(
    'text' => $data['text'],
    'create_time' => time(),
    );
    $this->db->where('id', $data['post_id'])
    ->update('post_base',$b_data);
    $this->db->where('post_base_id', $data['post_id'])
    ->where('floor',1)
    ->update('post_detail',$d_data);
    }*/

    /**
     * 删除帖子
     * @param $data
     * @return bool
     */
    public function delete_post($data){
        $d_data['delete'] = 1;
        return $this->db->where('id', $data['post_id'])
            ->update('post_base',$d_data)?
            TRUE:
            FALSE;
    }

    /**
     * 删除帖子  旧式查询方法 *2017/7/25 0025
     * @param $data
     * @return mixed
     */
    /**
     * public function delete_post($data){
    $d_data = array(
    '`delete`' => '1',
    );
    $this->db->where('id', $data['post_id'])
    ->update('post_base',$d_data);
    $data['floor'] = 1;
    $this->delete_post_reply($data);
    $rs['code']=1;
    return $rs;
    }*/


    /**
     * 查询帖子回复所在的页数
     * @param $p_id
     * @param $floor
     * @return bool|int
     *
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

    /**
     * 解析帖子内容，获得帖子中包含的图片
     * @param $rs
     * @return mixed
     */
    public function get_image_url($rs){
        for ($i=0; $i<count($rs['data']); $i++) {
            $rs['data'][$i]['content'] = str_replace('\"', '', $rs['data'][$i]['content']);
            preg_match_all('/<img[^>]*src\s?=\s?[\'|"]([^\'|"]*)[\'|"]/is', $rs['data'][$i]['content'], $picarr);
            $rs['data'][$i]['image']=$picarr['1'];
        }
        return $rs;
    }

    /**
     * 删除帖子中gif格式的图片
     * @param $data
     * @return mixed
     */
    public function delete_image_gif($data){
        $rs = $data;
        $datab = "/([http|https]):\/\/.*?\.gif/";
        foreach ($rs['data'] as $key1 => $value) {
            if(!empty($value['image'])){
                foreach ($value['image'] as $key2 => $image) {
                    if(preg_match($datab, $image)){
                        unset($rs['data'][$key1]['image'][$key2]);
                    }
                }
            }
        }
        return $rs;

    }

    /**
     * 展示图片限制，目前是显示三张
     * @param $data
     * @return mixed
     */
    public function post_image_limit($data){
        $rs=$data;
        foreach ($rs['data'] as $key => $value) {
            if(count($value['image'])>3){
                $rs['data'][$key]['image'] = array_slice($value['image'],0,3);
            }
        }
        return $rs;
    }

    /**
     * 从帖子内容去除 HTML 和 PHP 标记
     * @param $data
     * @return mixed
     */
    public function delete_html_posts($data){
        $rs = $data;
        for ($i=0; $i<count($rs['data']); $i++) {
            $rs['data'][$i]['content'] = strip_tags($rs['data'][$i]['content']);

        }
        return $rs;
    }

    /**
     * 帖子内容长度显示，目前是300字符
     * @param $data
     * @return mixed
     */
    public function post_text_limit($data){
        $rs=$data;
        for ($i=0; $i<count($rs['data']); $i++) {
            $rs['data'][$i]['content'] =mb_convert_encoding(substr($rs['data'][$i]['content'],0,299), 'UTF-8','GB2312,UTF-8');
        }
        return $rs;
    }

    /**
     * 评论帖子后通知被评论者
     * @param $replies
     * @param $post_info
     * @return bool
     */
    public function post_reply_message($replies,$post_info){
        if($replies['user_base_id']==$post_info['user_base_id']||$replies['reply_id']==$replies['user_base_id']){
            return false;
        }
        $data = array(
            'user_base_id' =>$replies['reply_id']?:$post_info['user_base_id'],
            'user_reply_id'=>$replies['user_base_id'],
            'post_base_id'=>$replies['post_base_id'],
            'reply_floor'=>$replies['reply_floor'],
            'create_time'=>time(),
            'status'=>0,
        );
        return $this->db->insert('message_reply',$data);
    }

    /**
     * 置顶帖子
     * @param $post_id
     * @return bool
     */
    public function sticky_post($post_id){
        $sticky = $this->get_post_information1($post_id)['sticky'];
        if($sticky==0){
            $param['sticky'] = 1;
        }else{
            $param['sticky'] = 0;
        }
        return $this->db->where('id',$post_id)->update('post_base', $param)?
            TRUE:
            FALSE;

    }

    /**
     * 取消置顶接口，已合并，后续会移除*2017/7/25 0025
     * @return bool
     */
    public function post_unsticky(){
        $data = array(
            'post_id' => $this->input->get('post_id'),
        );
        $param = array(
            'sticky' => 0,
        );

        if(!empty($data['post_id']))
        {
            $this->db->where('id',$data['post_id']);
            $re=$this->db->update('post_base', $param);
            return $re;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * 判断用户是否是发帖者，无用途，后续会移除*2017/7/25 0025
     * @param $user_id
     * @param $post_id
     * @return int
     */
    public function judge_poster($user_id,$post_id){
        $sql=$this->db->select('floor')
            ->from('post_detail')
            ->where('user_base_id',$user_id)
            ->where('post_base_id',$post_id)
            ->get()
            ->row_array();
        if($sql['floor']==1){
            $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }

    /**
     * 删除帖子  旧式查询方法 *2017/7/25 0025
     * @param $data
     * @return mixed
     */
    /**
     * public function delete_post($data){
        $d_data = array(
            '`delete`' => '1',
        );
        $this->db->where('id', $data['post_id'])
            ->update('post_base',$d_data);
        $data['floor'] = 1;
        $this->delete_post_reply($data);
        $rs['code']=1;
        return $rs;
    }*/

    /**
     * 锁定、接触锁定帖子
     * @param $post_id
     * @return bool
     */
    public function lock_post($post_id){
        $lock = $this->get_post_information1($post_id)['lock'];
        if($lock==0){
            $param['lock'] = 1;
        }else{
            $param['lock'] = 0;
        }
        return $this->db->where('id',$post_id)->update('post_base', $param)?
            TRUE:
            FALSE;

    }

    /**
     * 取消锁定接口，已合并，后续会移除*2017/7/25 0025
     * @return bool
     */
    public function unlock_post(){
        $data = array(
            'post_id' => $this->input->get('post_id'),
        );
        $param = array(
            'lock' => 0,
        );
        if(!empty($data['post_id']))
        {
            $this->db->where('id',$data['post_id']);
            $re=$this->db->update('post_base', $param);
            return $re;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * 判断用户是否收藏帖子
     * @param $data
     * @return mixed
     */
    public function check_collect_post($data){
        $sql=$this->db->select('*')
            ->from('user_collection')
            ->where('post_base_id',$data['post_id'])
            ->where('user_base_id',$data['user_id'])
            ->get()
            ->row_array();
        return $sql;
    }

    /**
     * 收藏帖子
     * @param $data
     * @return bool
     */
    public function collect_post($data){
        $i_data=array(
            'post_base_id'=>$data['post_id'],
            'user_base_id'=>$data['user_id'],
            'create_time'=>time(),
        );
        return $this->db->insert('user_collection',$i_data);
    }

    /**
     * 获得用户收藏帖子
     * @param $data
     * @param bool $paging
     * @return int|mixed
     */
    public function get_collect_post($data,$paging = FALSE){
        $select = 'pb.title,pc.content,uc.create_time,gb.`name`,pb.`delete`,gb.`delete` AS g_delete,uc.post_base_id,pb.group_base_id';
        $this->db->select($select);
        $this->db->join('post_base AS pb','uc.post_base_id = pb.id');
        $this->db->join('post_content AS pc','pc.post_base_id = uc.post_base_id');
        $this->db->join('group_base AS gb','pb.group_base_id = gb.id');
        $this->db->order_by('uc.create_time ','DESC');
        $this->db->where("uc.user_base_id = {$data['user_id']} ");
        $this->db->where('uc.delete = 0');
        if($paging){
            return $this->db->count_all_results('user_collection AS uc');
        }else{
            return $this->db->get('user_collection AS uc',$data['limit'],$data['offset'])->result_array();
        }
    }
    /**
     * 获取用户收藏帖子  旧式查询方法  后续会移除 *2017/7/28 0028
     * @param $data
     * @return array
     */
    /**
     * public function get_collect_post($data){
        $num=20;
        if($data['user_id']==null){
            $data['user_id']=0;
        }
        $user_id=$data['user_id'];
        $rs=array();
        $sql = "SELECT ceil(count(*)/$num) AS page_count "
            . 'FROM user_collection '
            . "WHERE user_collection.user_base_id='$user_id' AND user_collection.delete=0 ";
        $page_count=$this->db->query($sql)->result_array()[0];

        $rs['page_count'] = (int)$page_count['page_count'];
        if ($rs['page_count'] == 0 ){
            $rs['page_count']=1;
        }
        if($data['page'] > $rs['page_count']){
            $data['page'] = $rs['page_count'];
        }elseif ($data['page']==null){
            $data['page']=1;
        }
        $start=($data['page']-1)*$num;
        $rs['current_page'] = (int)$data['page'];
        $sql = 'SELECT pb.id AS post_id,uc.create_time,pb.title AS p_title,gb.id AS group_id,gb.name AS g_name,ub.nickname AS user_name,pb.delete,pd.text AS p_text '
            . 'FROM user_collection uc,post_base pb,group_base gb,user_base AS ub,post_detail pd '
            . "WHERE pb.id=uc.post_base_id AND pb.group_base_id=gb.id AND uc.delete=0 AND uc.user_base_id=$user_id AND uc.delete=0 AND pb.user_base_id=ub.id AND pd.post_base_id = uc.post_base_id AND pd.floor=1 "
            . "LIMIT $start,$num ";
        $this->db->flush_cache();
        $rs['posts']=$this->db->query($sql)->result_array();
        foreach ($rs['posts'] as $key => $value) {
            $rs['posts']["$key"]['create_time']=date('Y-m-d H:i:s',$rs['posts']["$key"]['create_time']);
        }
        return $rs;
    }*/

    /**
     * 取消帖子收藏接口，已合并，后续会移除*2017/7/25 0025
     * @param $data
     * @return mixed
     *
     */
    /**
     *    public function delete_collect_post($data){
        $param = array(
            'delete' => 1,
        );
        if(!(empty($data['post_id'])||empty($data['user_id'])))
        {
            $this->db->where('post_base_id',$data['post_id'])
                ->where('user_base_id',$data['user_id']);
            $re=$this->db->update('user_collection', $param);
            return $re;
        }
        else
        {
            return FALSE;
        }
    }*/

    /**
     * 获得用户点赞
     * @param $data
     * @return mixed
     */
    public function get_approve_post($data){
        $sql=$this->db->select('*')
            ->from('post_approved')
            ->where('post_base_id',$data['post_id'])
            ->where('user_base_id',$data['user_id'])
            ->where('floor',$data['floor'])
            ->get()
            ->row_array();
        return $sql;
    }

    /**
     * 更新收藏
     * @param $data
     * @param $post_exist
     * @return bool
     */
    public function update_collect_post($data,$post_exist){
        $delete = $this->check_collect_post($data)['delete'];
        if($post_exist&&$delete){
            $field['delete'] = 0;
        }else{
            $field['delete'] = 1;
        }
        if(!$post_exist&&$delete){
            return FALSE;
        }
        $field['create_time'] = time();
        return $this->db->where('post_base_id',$data['post_id'])
            ->where('user_base_id',$data['user_id'])
            ->update('user_collection',$field);
    }

    /**
     * 更新点赞
     * @param $data
     * @return bool
     */
    public function update_approve_post($data){
        $approved = $this->get_approve_post($data);
        if($approved['approved']){
            $field = array('approved'=>0);
        }else{
            $field = array('approved'=>1);
        }
        return $this->db->where('post_base_id',$data['post_id'])
            ->where('user_base_id',$data['user_id'])
            ->where('floor',$data['floor'])
            ->update('post_approved',$field);
    }

    /**
     * 增加点赞
     * @param $data
     * @return bool
     */
    public function add_approve_post($data){
        $field = array(
            'user_base_id' => $data['user_id'],
            'post_base_id' => $data['post_id'],
            'floor'   => $data['floor'],
            'approved' => 1,
        );
        return $this->db->insert('post_approved',$field);
    }

    /**
     * 判断用户是否是帖子层主
     * @param $user_id
     * @param $post_id
     * @param $floor
     * @return mixed
     */
    public function judge_post_reply_user($user_id,$post_id,$floor){
        $sql=$this->db->select('post_base_id')
            ->from('post_comment')
            ->where('post_base_id',$post_id)
            ->where('user_base_id',$user_id)
            ->where('floor',$floor)
            ->get()
            ->row_array();
        return $sql?TRUE:FALSE;
    }

    /**
     * 删除帖子回复
     * @param $data
     * @return bool
     */
    public function delete_post_reply($data){
        return $this->db->where('post_base_id',$data['post_id'])
            ->where('floor',$data['floor'])
            ->update('post_comment',['delete'=>1]);

    }


}
