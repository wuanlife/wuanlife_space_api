<?php

class Domain_Post {

    /*
    帖子预览文本限制100
     */
    public function postTextLimit($data){
        $rs=$data;
        for ($i=0; $i<count($rs['posts']); $i++) {
        $rs['posts'][$i]['text'] =mb_convert_encoding(substr($rs['posts'][$i]['text'],0,299), 'UTF-8','GB2312,UTF-8');
        }
        return $rs;
    }
 /*
  * 判断是否为发帖者
  */
    public function judgePoster($user_id,$post_id){
        $model = new Model_Post();
        $rs = $model->judgePoster($user_id,$post_id);
        return $rs;
    }

/*
 * 删除帖子列表html
 */
    public function deleteHtmlPosts($data){
        $rs = $data;
        for ($i=0; $i<count($rs['posts']); $i++) {
        $rs['posts'][$i]['text'] = strip_tags($rs['posts'][$i]['text']);

        }
        return $rs;
    }

/*
 * 提取帖子列表中文本部分的html标签
 */
    public function getImageUrl($data){
        $rs = $data;
        for ($i=0; $i<count($rs['posts']); $i++) {
        $rs['posts'][$i]['text'] = str_replace('\"', '', $rs['posts'][$i]['text']);
        preg_match_all('/<img[^>]*src\s?=\s?[\'|"]([^\'|"]*)[\'|"]/is', $rs['posts'][$i]['text'], $picarr);
        $rs['posts'][$i]['image']=$picarr['1'];
        }
        return $rs;
    }

/*
 * 删除帖子回复html
 */
    public function deleteHtmlReply($data){
        $rs = $data;
        for ($i=0; $i<count($rs['reply']); $i++) {
        $rs['reply'][$i]['text'] = strip_tags($rs['reply'][$i]['text']);
        }
        return $rs;
    }
/*
 * 获取创建者id
 */
    public function getCreaterId($groupID){
        $model = new Model_Post();
        $createrId = $model->getCreaterId($groupID);
        return $createrId;
    }
/*
 * 获取星球id
 */
    public function getGroupId($post_id){
        $model = new Model_Post();
        $group_Id = $model->getGroupId($post_id);
        return $group_Id;
    }

 /*
  * 过滤帖子列表image中gif格式的url
  */
    public function deleteImageGif($data)
    {
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
    public function postImageLimit($data){
        $rs=$data;
        foreach ($rs['posts'] as $key => $value) {
            if(count($value['image'])>3){
                $rs['posts'][$key]['image'] = array_slice($value['image'],0,3);
            }
        }
        return $rs;
    }

    public function getIndexPost($page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getIndexPost($page);
        return $rs;
    }

    public function getGroupPost($groupID,$page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getGroupPost($groupID,$page);
        return $rs;
    }

    public function getMyGroupPost($userID,$page) {
        $rs = array();
        $model = new Model_Post();
        $model1 = new Model_Group();
        $rs = $model->getMyGroupPost($userID,$page);
        $rs['user_name'] = $model1->getUser($userID);
        return $rs;
    }

    public function getPostBase($postID) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getPostBase($postID);
        return $rs;
    }

    public function getPostReply($postID,$page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getPostReply($postID,$page);
        return $rs;
    }

    public function PostReply($data) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->PostReply($data);
        return $rs;
    }

    public function editPost($data) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->editPost($data);
        return $rs;
    }
/*
    public function stickyPost($data){
    	$rs = array();
    	$domain = new Domain_Post();
        $domain1 = new Domain_User();
    	$sqla = $domain->getGroupId($data['post_id']);
        $sqlb = $domain1->judgeCreate($data['user_id'],$sqla);
        if($sqlb) {
            $model=new Model_Post();
            $rs = $model->stickyPost($data);

        }else{
            $rs['code']=0;
            $rs['re']="仅星球创建者能置顶帖子!";
        }
        return $rs;
    }
*/	
	public function stickyPost($data){
    	$rs = array();
    	$model=new Model_Post();
        $rs = $model->stickyPost($data);
        if($rs) {
            $rs['code']=1;
            $rs['re']="置顶帖子成功!";
        }else{
            $rs['code']=0;
            $rs['re']="操作过于频繁!";
        }
        return $rs;
    }
	public function unstickyPost($data){
    	$rs = array();
    	$model=new Model_Post();
        $rs = $model->unstickyPost($data);
        if($rs) {
            $rs['code']=1;
            $rs['re']="取消置顶帖子成功!";
        }else{
            $rs['code']=0;
            $rs['re']="操作过于频繁!";
        }
        return $rs;
    }
/*
    public function unStickyPost($data){
        $rs = array();
        $domain = new Domain_Post();
        $domain1 = new Domain_User();
        $sqla = $domain->getGroupId($data['post_id']);
        $sqlb = $domain1->judgeCreate($data['user_id'],$sqla);

        if($sqlb) {
            $model=new Model_Post();
            $rs = $model->unStickyPost($data);

        }else{
            $rs['code']=0;
            $rs['re']="仅星球创建者能取消置顶帖子!";
        }
        return $rs;
    }
*/
    public function deletePost($data){
        $rs = array();
        $domain = new Domain_Post();
        $domain1 = new Domain_User();
        $sqla = $domain->getGroupId($data['post_id']);
        $sqlb = $domain1->judgeCreate($data['user_id'],$sqla);
        $sqlc = $domain->judgePoster($data['user_id'],$data['post_id'],$sqla);
        $sqld = $domain1->judgeAdmin($data['user_id']);
        if($sqlb||$sqlc||$sqld){
            $model = new Model_Post();
            $rs = $model->deletePost($data);
        }else{
            $rs['code']=0;
            $rs['re']="仅星球创建者和发帖者和管理员能删除帖子!";
        }
    	return $rs;
    }
/*
    public function lockPost($user_id,$post_id){
        $domain = new Domain_Post();
        $domain1 = new Domain_User();
        $common=new Domain_Common();
        $sqla = $domain->getGroupId($post_id);
        $sqlb = $domain1->judgeCreate($user_id,$sqla);
        $sqlc=$common->judgePostUser($user_id,$post_id);
        if($sqlb||$sqlc) {
            $model=new Model_Post();
            $rs['code']=$model->lockPost($post_id);
            $rs['re']="操作成功";
        }else{
            $rs['code']=0;
            $rs['re']="权限不足";
        }
        return $rs;
    }
*/
	public function lockPost($user_id,$post_id){
        $model = new Model_Post();
        $rs = $model->lockPost($post_id);
        if($rs) {
            $info['code']=1;
            $info['re']="锁帖成功！";
        }else{
            $info['code']=0;
            $info['re']="操作过于频繁！";
        }
        return $info;
    }
	public function unlockPost($user_id,$post_id){
        $model = new Model_Post();
        $rs = $model->unlockPost($post_id);
        if($rs) {
            $info['code']=1;
            $info['re']="锁帖成功！";
        }else{
            $info['code']=0;
            $info['re']="操作过于频繁！";
        }
        return $info;
    }
/*	
    public function unlockPost($user_id,$post_id){
        $domain = new Domain_Post();
        $domain1 = new Domain_User();
        $common=new Domain_Common();
        $sqla = $domain->getGroupId($post_id);
        $sqlb = $domain1->judgeCreate($user_id,$sqla);
        $sqlc=$common->judgePostUser($user_id,$post_id);
        if($sqlb||$sqlc) {
            $model=new Model_Post();
            $rs['code']=$model->unlockPost($post_id);
            $rs['re']="操作成功";

        }else{
           $rs['code']=0;
            $rs['re']="权限不足";
       }
          return $rs;
      }
*/
    public function searchPosts($text,$pnum,$pn){
       $model=new Model_Post();
        $re=$model->searchPosts($text,$pnum,$pn);
        $page_num=$pnum;
        $all_num=$model->searchPostsNum($text);
        $page_all_num =ceil($all_num/$page_num);                //总页数
        if ($page_all_num == 0){
            $page_all_num =1;
        }
        $re['PostsPage']=$page_all_num;
        return $re;
    }

    public function collectPost($user_id,$post_id){
        $model = new Model_Post();
        $rs = $model->collectPost($user_id,$post_id);
        if($rs) {
            $info['code']=1;
            $info['re']="收藏成功！";
        }else{
            $info['code']=0;
            $info['re']="操作过于频繁！";
        }
        return $info;
    }

    public function getCollectPost($user_id,$page) {
        $rs = array();
        $model = new Model_Post();
        $model1 = new Model_Group();
        $rs = $model->getCollectPost($user_id,$page);
        return $rs;
    }



}
