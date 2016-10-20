<?php

class Model_Post extends PhalApi_Model_NotORM {

    public function getIndexPost($page) {

        $num=30;
        $rs   = array();

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . "FROM post_base pb,group_base gb WHERE pb.delete=0 AND pb.group_base_id=gb.id AND gb.private='0'";

        $params = array(':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
		if($page > $rs['pageCount']){
			$page = $rs['pageCount'];
		}
        $rs['currentPage'] = $page;
        $sql = 'SELECT pb.id AS postID,pb.title,pd.text,pb.lock,pd.createTime,ub.nickname,gb.id AS groupID,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete='0' AND gb.private='0' "
             . 'GROUP BY pb.id '
             . 'ORDER BY MIN(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->user_base->queryAll($sql, $params);

        return $rs;
    }

    public function getGroupPost($groupID,$page) {

        $num=30;
        $rs   = array();
        $groupData=DI()->notorm->group_base
        ->select('id as groupID,name as groupName')
        ->where('id =?',$groupID)
        ->fetchAll();
        if(empty($groupData)){
             throw new PhalApi_Exception_BadRequest('星球不存在！');
        }
        $rs['groupID'] = $groupData['0']['groupID'];
        $rs['groupName'] = $groupData['0']['groupName'];


        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb '
             . 'WHERE pb.group_base_id=gb.id AND gb.id=:group_id AND pb.delete=0 ';

        $params = array(':group_id' =>$groupID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
		if($page > $rs['pageCount']){
			$page = $rs['pageCount'];
		}
        $rs['currentPage'] = $page;
        $sql = 'SELECT  pb.digest,pb.id AS postID,pb.title,pd.text,pd.createTime,ub.id,ub.nickname,pb.sticky,pb.lock '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id=:group_id AND pb.delete=0 '
             . 'GROUP BY pb.id '
             . 'ORDER BY pb.sticky DESC, '
             . 'MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':group_id' =>$groupID,':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);
        return $rs;
    }

    public function getMyGroupPost($userID,$page) {

        $num=30;
        $rs   = array();

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb,group_detail gd '
             . 'WHERE pb.group_base_id=gb.id AND gb.id=gd.group_base_id AND gd.user_base_id=:user_id AND pb.delete=0 ';

        $params = array(':user_id' =>$userID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
		if($page > $rs['pageCount']){
			$page = $rs['pageCount'];
		}
        $rs['currentPage'] = $page;
        $sql = 'SELECT  pb.id AS postID,pb.title,pd.text,pb.lock,pd.createTime,ub.nickname,gb.id AS groupID,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete=0 '
             . 'AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id =:user_id )'
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
              . 'LIMIT :start,:num ';
        $params = array(':user_id' =>$userID,':start' =>($page-1)*$num , ':num' =>$num );
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);
        return $rs;
    }

    public function getPostBase($postID) {
        $rs   = array();
        $sql = 'SELECT pb.id AS postID,gb.id AS groupID,gb.name AS groupName,pb.title,pd.text,ub.id,ub.nickname,pd.createTime,pb.sticky,pb.lock '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.delete=0 AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.id=:post_id AND pd.floor=1' ;
        $params = array(':post_id' =>$postID );
        $rs = DI()->notorm->post_base->queryAll($sql, $params);
        if (empty($rs)) {
            throw new PhalApi_Exception_BadRequest('帖子不存在！');
            //$rs[0]['msg']="帖子不存在";
            //die('出错了！');
        }else {
            $rs[0]['sticky']=(int)$rs[0]['sticky'];
            $rs[0]['lock']=(int)$rs[0]['lock'];
            preg_match_all("(http://[-a-zA-Z0-9@:%_\+.~#?&//=]+[.jpg.gif.png])",$rs[0]['text'],$rs[0]['p_image']);
            /*
            $p_image = array();
            $results = DI()->notorm->post_image
                ->select('p_image,post_image_id')
                ->where('post_base_id =?', $postID)
                ->AND('post_image.delete=?','0');
                // ->fetchall();

            foreach ($results as $key => $row) {
                $p_image[$key] = array("id"=>(int)$row['post_image_id'],"URL"=>"http://".$_SERVER['HTTP_HOST'].$row['p_image']);
            }
            $rs[0]['p_image']=$p_image;
            if(empty($p_image)){
                $rs[0]['p_image']=NULL;
            }
            */
        }
        return $rs;

    }

    public function getPostReply($postID,$page) {

        $num=30;
        $rs   = array();

        $rs['postID']=$postID;
        $sql = 'SELECT ceil(count(pd.post_base_id)/:num) AS pageCount,count(*) AS replyCount '
         . 'FROM post_detail as pd '
         . 'WHERE pd.post_base_id=:post_id AND pd.floor>1 ';

        $params = array(':post_id' =>$postID,':num' =>$num);
        $count = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['replyCount'] = (int)$count[0]['replyCount'];
        $rs['pageCount'] = (int)$count[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
		if($page > $rs['pageCount']){
			$page = $rs['pageCount'];
		}
        $rs['currentPage'] = $page;
		$rs['reply']= DI()->notorm->post_detail
        ->SELECT('post_detail.text,user_base.nickname,post_detail.createTime,post_detail.floor')
        ->WHERE('post_detail.post_base_id = ?',$postID)
        ->AND('post_detail.floor > ?','1')
        ->order('post_detail.floor ASC')
        ->limit(($page-1)*$num,$num)
        ->fetchALL();
        return $rs;
    }
    public function PostReply($data) {
        $rs = array();
        $time = date('Y-m-d H:i:s',time());
        //查询最大楼层
        $sql=DI()->notorm->post_detail
        ->select('post_base_id,user_base_id,max(floor)')
        ->where('post_base_id =?',$data['post_base_id'])
        ->fetchone();
        $data['createTime'] = $time;
        $data['floor'] = ($sql['max(floor)'])+1;
        $rs = DI()->notorm->post_detail->insert($data);
        return $rs;
    }
    public function editPost($data) {
        $rs = array();
        $time = date('Y-m-d H:i:s',time());
        $b_data = array(
                'title' => $data['title'],
        );
        $d_data = array(
                'text' => $data['text'],
                'createTime' => $time,
        );
        $sql=DI()->notorm->post_detail
        ->select('user_base_id')
        ->where('post_base_id =?',$data['post_base_id'])
        ->fetchone();
        if($data['user_id']==$sql['user_base_id']) {
            $pb = DI()->notorm->post_base
            ->where('id =?', $data['post_base_id'])
            ->update($b_data);
            $pd = DI()->notorm->post_detail
            ->where('post_base_id =?', $data['post_base_id'])
            ->AND('post_detail.floor = ?','1')
            ->update($d_data);
            if(!empty($data['post_image_id'])){
                $delimage = DI()->notorm->post_image
                ->where('post_image_id =?', $data['post_image_id'])
                ->AND('post_image.post_base_id = ?',$data['post_base_id'])
                ->update(array('`delete`'=>'1'));
            }
/*          $domain = new Domain_Group();
            $pei = array("id"=>$data['post_base_id']);
            foreach ($data['p_image'] as $key => $value) {
                if(!empty($value)) {
                    $fileName = $domain->doFileUpload($key,$value);
                    $pi = $domain->saveData($fileName,$value,$pei);
                }
                else {
                    $pi = NULL;
                }
            }*/
            $rs['code']=1;
            $rs['info']['post_base_id']=$data['post_base_id'];
            $rs['info']['user_base_id']=$data['user_id'];
            $rs['info']['title']=$data['title'];
            $rs['info']['text']=$data['text'];
            $p_image = array();
            $results = DI()->notorm->post_image
            ->select('p_image,post_image_id')
            ->where('post_base_id =?', $data['post_base_id'])
            ->AND('post_image.delete=?','0');
            // ->fetchall();
            /*不需要返回URL值
            foreach ($results as $key => $row) {
                $p_image[$key] = array("id"=>(int)$row['post_image_id'],"URL"=>"http://".$_SERVER['HTTP_HOST'].$row['p_image']);
            }
            if(empty($p_image)){
                $p_image=NULL;
            }
            $rs['info']['URL']=$p_image;
            */
            $rs['info']['floor']=1;
            $rs['info']['createTime']=$time;
        }else{
            $rs['code']=0;
            $rs['msg']="您没有权限!";
        }
        return $rs;
    }
    protected function getTableName($id) {
        return 'user';
    }

    public function stickyPost($data){
        $rs = array();

        $s_data = array(
            'sticky' => '1',
        );
        $s = DI()->notorm->post_base
            ->where('id =?', $data['post_id'])
            ->update($s_data);
        $rs['code']=1;
        $rs['re']="操作成功";

        return $rs;
    }

    public function unStickyPost($data){
        $rs = array();

        $s_data = array(
                'sticky' => '0',
        );
            $s = DI()->notorm->post_base
            ->where('id =?', $data['post_id'])
            ->update($s_data);
            $rs['code']=1;
            $rs['re']="操作成功";

        return $rs;
    }

    public function deletePost($data){
        $rs = array();

        $d_data = array(
                '`delete`' => '1',
        );

            $sa = DI()->notorm->post_base
            ->where('id =?', $data['post_id'])
            ->update($d_data);
            /*$sb = DI()->notorm->post_detail
            ->where('post_base_id=?', $data['post_id'])
            ->update($d_data);*/
            $rs['code']=1;
            $rs['re']="操作成功";
        return $rs;
    }

    public function getCreaterId($groupID){
        $createrId=DI()->notorm->group_detail
        ->select('user_base_id')
        ->where('group_base_id=?',$groupID)
        ->and('authorization=?','01')
        ->fetchone();
        return $createrId;
        }

    public function getGroupId($post_id){
        $sqla=DI()->notorm->post_base
            ->select('group_base_id')
            ->where('id=?',$post_id)
            ->fetchone();
        return $sqla['group_base_id'];
    }

    public function judgePoster($user_id,$post_id){
        $sql=DI()->notorm->post_detail->select('floor')->where('user_base_id=?',$user_id)->where('post_base_id=?',$post_id)->fetch();
        if($sql['floor']==1){
            $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }


    public function judgePostExist($post_id){
        $sql=DI()->notorm->post_detail->select('post_base_id')->where('post_base_id= ?',$post_id)->fetch();
        if(!empty($sql)){
            $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }

    public function lockPost($post_id){
        $data = array(
            '`lock`' => '1',
        );
        $sql=DI()->notorm->post_base->where('id',$post_id)->update($data);
        return $sql;
    }

    public function unlockPost($post_id){
        $data = array(
            '`lock`' => '0',
        );
        $sql=DI()->notorm->post_base->where('id',$post_id)->update($data);
        return $sql;
    }

    public function judgePostUser($user_id,$post_id){
        $sql=DI()->notorm->post_base->where('id',$post_id)->where('user_base_id',$user_id)->fetch();
        return $sql;
    }

    public function judgePostLock($post_id){
        $sql=DI()->notorm->post_base->where('id=?',$post_id)->fetch();
        return $sql['lock'];
    }
    public function searchPosts($text,$pnum,$pn){
        if(empty($pn)){
            $rs['posts'] = array();
            return $rs;
        }
        $num=($pn-1)*$pnum;
        $sql = 'SELECT pb.id AS postID,pb.title,pd.text,pb.lock,pd.createTime,ub.nickname,gb.id AS groupID,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete='0' AND gb.private='0' "
             . "AND pb.title LIKE '%$text%' "
             . 'GROUP BY pb.id '
             . 'ORDER BY COUNT(pd.post_base_id) DESC '
             . "LIMIT $num,$pnum";
        $rs['posts'] = DI()->notorm->user_base->queryAll($sql);
        return $rs;


    }

    public function searchPostsNum($text){
        $sql='SELECT COUNT(post_base.id) AS num FROM post_base '
            ."where post_base.title LIKE '%$text%' AND post_base.delete='0'";
        $re = $this->getORM()->queryAll($sql);
        return $re[0]['num'];
    }
}

