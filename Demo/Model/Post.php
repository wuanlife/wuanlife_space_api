<?php

class Model_Post extends PhalApi_Model_NotORM {

    public function getIndexPost($page) {

        $num=30;
        $rs   = array();
        $sql = 'SELECT pb.id AS postID,pb.title,pd.text,pd.createTime,ub.nickname,gb.id AS groupID,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . "WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete='0' "
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->user_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . "FROM post_base WHERE post_base.delete='0'";

        $params = array(':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getGroupPost($groupID,$page) {

        $num=30;
        $rs   = array();
        $groupData=DI()->notorm->group_base
        ->select('id as groupID,name as groupName')
        ->where('id =?',$groupID)
        ->fetchAll();

        $rs['groupID'] = $groupData['0']['groupID'];
        $rs['groupName'] = $groupData['0']['groupName'];

        $sql = 'SELECT  pb.digest,pb.id AS postID,pb.title,pd.text,pd.createTime,ub.id,ub.nickname,pb.sticky '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id=:group_id AND pb.delete=0 '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':group_id' =>$groupID,':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(:pb.id)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb '
             . 'WHERE pb.group_base_id=gb.id AND gb.id=:group_id ';

        $params = array(':group_id' =>$groupID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getMyGroupPost($userID,$page) {

        $num=30;
        $rs   = array();
        $sql = 'SELECT  pb.id AS postID,pb.title,pd.text,pd.createTime,ub.nickname,gb.id AS groupID,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.delete=0 '
             . 'AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id =:user_id )'
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
              . 'LIMIT :start,:num ';
        $params = array(':user_id' =>$userID,':start' =>($page-1)*$num , ':num' =>$num );
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(:pb.id)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb,group_detail gd '
             . 'WHERE pb.group_base_id=gb.id AND gb.id=gd.group_base_id AND gd.user_base_id=:user_id AND pb.delete=0 ';

        $params = array(':user_id' =>$userID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getPostBase($postID) {
        $rs   = array();
        $sql = 'SELECT pb.id AS postID,gb.id AS groupID,gb.name AS groupName,pb.title,pd.text,ub.id,ub.nickname,pd.createTime,pb.sticky '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.id=:post_id AND pd.floor=1' ;
        $params = array(':post_id' =>$postID );
        $rs = DI()->notorm->post_base->queryAll($sql, $params);
	    if (empty($rs)) {
            $rs=NULL;
        }
        $p_image = array();
        $results = DI()->notorm->post_image
            ->select('p_image,post_image_id')
            ->where('post_base_id =?', $postID)
            ->AND('post_image.delete=?','0');
            // ->fetchall();
        foreach ($results as $key => $row) {
            $p_image[$key] = array($row['post_image_id']=>$_SERVER['HTTP_HOST'].$row['p_image']);
        }
		$rs[0]['p_image']=$p_image;
		if(empty($p_image)){
			$rs[0]['p_image']=NULL;
		}
        return $rs;
    }

    public function getPostReply($postID,$page) {

        $num=30;
        $rs   = array();
        $rs['reply']= DI()->notorm->post_detail
        ->SELECT('post_detail.text,user_base.nickname,post_detail.createTime')
        ->WHERE('post_detail.post_base_id = ?',$postID)
        ->AND('post_detail.floor > ?','1')
        ->order('post_detail.floor ASC')
        ->limit(($page-1)*$num,$num)
        ->fetchALL();

        $rs['postID']=$postID;
        $sql = 'SELECT ceil(count(:pb.id)/:num) AS pageCount,count(*) AS replyCount '
         . 'FROM post_detail '
         . 'WHERE post_base_id=:post_id AND post_detail.floor>1 ';

        $params = array(':post_id' =>$postID,':num' =>$num);
        $count = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['replyCount'] = (int)$count[0]['replyCount'];
        $rs['pageCount'] = (int)$count[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
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
            if(!empty($data["p_image"])) {
				//创建上传路径
				$date=date("Y/m/d");
				$RootDIR = dirname(__FILE__);
				$path=$RootDIR."/../../Public/demo/upload/posts/$date/";
				$base64_image_string = $data["p_image"];
				$output_file_without_extentnion = time();
				$path_with_end_slash = "$path";
				if(!is_readable($path)) {
					is_file($path) or mkdir($path,0777,true);
				}
				//调用接口保存base64字符串为图片
				$domain = new Domain_Group();
				$filepath = $domain->save_base64_image($base64_image_string, $output_file_without_extentnion, $path_with_end_slash );
				$size = getimagesize ($filepath);
				if($size[0]>94&&$size[1]>94){
					include "../../Library/resizeimage.php";
					$imageresize = new ResizeImage($filepath, 94, 94,1, $filepath);//裁剪图片
				}
				$data["p_image"] = substr($filepath,-39);
				$i_data = array(
					'post_base_id'=> $data['post_base_id'],
					'p_image'=> $data['p_image'],
				);
				//新增一张图片
				$pi = DI()->notorm->post_image->insert($i_data);
			}
            $rs['code']=1;
            $rs['info']['post_base_id']=$data['post_base_id'];
            $rs['info']['user_base_id']=$data['user_id'];
            $rs['info']['title']=$data['title'];
            $rs['info']['text']=$data['text'];
			$p_image = array();
			$results = DI()->notorm->post_image
            ->select('p_image')
            ->where('post_base_id =?', $data['post_base_id'])
            ->AND('post_image.delete=?','0');
            // ->fetchall();
			foreach ($results as $key => $row) {
				$p_image[$key] = $_SERVER['HTTP_HOST'].$row['p_image'];
			}
			if(empty($p_image)){
				$p_image=NULL;
			}
			$rs['info']['URL']=$p_image;
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
        return $sqla;
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
}
   
