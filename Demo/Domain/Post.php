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

    public function getPostDetail($postID,$page) {
        $rs = array();
        $model = new Model_Post();
        $rs = $model->getPostDetail($postID,$page);
        return $rs;
    }

}
