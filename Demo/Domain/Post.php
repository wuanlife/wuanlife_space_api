<?php

class Domain_Post {

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
        $rs = $model->getMyGroupPost($userID,$page);
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
    
    public function stickyPost($data){
    	$rs = array();
    	$model = new Model_Post();
    	$rs = $model->stickyPost($data);
    	return $rs;
    }
    	
    public function unStickyPost($data){
    	$rs = array();
    	$model = new Model_Post();
    	$rs = $model->unStickyPost($data);
    	return $rs;
    }
    		
    public function deletePost($data){
    	$rs = array();
    	$model = new Model_Post();
    	$rs = $model->deletePost($data);
    	return $rs;
    }
    
    public function deleteHtmlPosts($data){
        $rs = $data;
        for ($i=0; $i<count($rs['posts']); $i++) {
        $rs['posts'][$i]['text'] = strip_tags($rs['posts'][$i]['text']);
        }
        return $rs;
    }

    public function deleteHtmlReply($data){
        $rs = $data;
        for ($i=0; $i<count($rs['reply']); $i++) {
        $rs['reply'][$i]['text'] = strip_tags($rs['reply'][$i]['text']);
        }
        return $rs;
    }

    public function getCreaterId($groupID){
        $model = new Model_Post();
        $createrId = $model->getCreaterId($groupID);
        return $createrId;
    }
}
