<?php

class Model_User extends PhalApi_Model_NotORM {
    protected function getTableName($id){
    return 'user_base';}

/*
 * 通过邮箱查找用户相关信息
 */

    public function userEmail($data) {
        $rs =DI()->notorm->user_base->select('*')->where('Email = ?',$data['Email'])->fetch();
        return $rs;
    }

/*
 * 通过用户id查找用户相关信息
 */
    public function userid($id) {
        $rs =DI()->notorm->user_base->select('*')->where('id = ?',$id)->fetch();
        return $rs;
    }
/*
 * 通过昵称查找用户相关信息
 */
    public function usernickname($data) {
        $rs =DI()->notorm->user_base->select('*')->where('nickname = ?',$data['nickname'])->fetch();

        return $rs;
    }

/*
 * 用户注册
 */
    public function reg($data)
    {
        $data['password'] = md5($data['password']);
        $rs=DI()->notorm->user_base->insert($data);
        $data_detail = array(
            'user_base_id'   =>$rs['id'],
            'authorization'  =>'01',
        );
        $sql=DI()->notorm->user_detail->insert($data_detail);
        return $rs;
    }

/*
 * 注销用户登录状态
 */
    public function logout()
    {
        /*
        用cookie保存用户登录状态
        $nickname = DI()->cookie->get('nickname');
        $userID = DI()->cookie->get('userID');
        $Email = DI()->cookie->get('Email');
        if(empty($nickname)&&empty($userID)&&empty($Email)){
            $this->code ='0';
            $this->msg = '未登录，无需注销！';
        }
        else{
            DI()->cookie->delete('nickname');
            DI()->cookie->delete('userID');
            DI()->cookie->delete('Email');
            $this->code ='1';
            $this->msg = '注销成功！';
        }
        return $this;
        */
        $this->code ='1';
        $this->msg = '注销成功！';
        return $this;
    }

    public function judgeUserExist($user_id){
        $sql=DI()->notorm->user_base->select('id')->where('id= ?',$user_id)->fetch();
        if(!empty($sql)){
            $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }

    public function judgeAdmin($user_id){
        $sql=DI()->notorm->user_detail->select('authorization')->where('user_base_id= ?',$user_id)->and('authorization=?',02)->fetch();
        if(!empty($sql)){
                $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }

    public function judgeCreate($user_id,$group_id){
        $sql=DI()->notorm->group_detail->select('user_base_id')->where('user_base_id= ?',$user_id)->where('group_base_id=?',$group_id)->and('authorization=?',01)->fetch();
        if(!empty($sql)){
            $rs=1;
        }else{
            $rs=0;
        }
        return $rs;
    }

/*
 * 通过用户id查找用户所有的信息
 */
    public function getUserInfo($user_id){
        $sql=DI()->notorm->user_detail->select('user_base_id as userID','sex','year','month','day','mailChecked','profile_picture')->where('user_base_id=?',$user_id)->fetch();
        $sqlb=DI()->notorm->user_base->select('Email','nickname')->where('id=?',$user_id)->fetch();
        if(empty($sql['profile_picture'])){
                $sql['profile_picture'] = 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
                //给无头像的用户加上默认头像
        }
        $sql['Email']=$sqlb['Email'];
        $sql['nickname']=$sqlb['nickname'];
        return $sql;
    }

    public function alterUserInfo($id,$data){
        $field=array(
            'profile_picture'=>$data['profile_picture'],
            'sex'=>$data['sex'],
            'year'=>$data['year'],
            'month'=>$data['month'],
            'day'=>$data['day']);
        $sql=DI()->notorm->user_detail->where('user_base_id=?',$id)->update($field);
        if(isset($sql)){
            $re['code']=1;
            $re['msg']='资料修改成功!';
        }else{
            $re['code']=0;
            $re['msg']='资料修改失败!';
        }
        if(!empty($data['nickname'])){
            $field=array('nickname'=>$data['nickname']);
            $user = $this->usernickname($field);
            if(empty($user)){
                $sqla=DI()->notorm->user_base->where('id=?',$id)->update($field);
            }else{
                if($user['id']!=$id){
                    $re['code']=0;
                    $re['msg']='用户名被占用，其他资料修改成功！';
                }
            }
        }
        return $re;
    }

/*
 * 用户邮箱校验
 */
    public function mailChecked($data,$mailChecked) {
        $row = $this->userEmail($data);
        $sql =DI()->notorm->user_detail->where('user_base_id = ?',$row['id'])->update($mailChecked);
        return $row;
    }

/*
 * 发送邮件验证码
 */
    public function SendMail($data){
        $data['Email'] = stripslashes(trim($data['Email']));
        $domain = new Domain_User();
        $data['Email'] = $domain->injectChk($data['Email']);
        $rs = $this->userEmail($data);
        return $rs;
    }

/*
 * 更新验证码
 */
    public function updatecode($code,$data){
        $sql = $this->userEmail($data);
        $num = $this->getcode($data);
        if($num['id']){
            $row = DI()->notorm->user_code->where(array('id = ?'=>$sql['id'],'difference = ?'=>$data['num']))->update($code);
        }else{
            $code['id'] = $sql['id'];
            $row = DI()->notorm->user_code->insert($code);
        }
    }

/*
 * 获取数据库保存的验证码
 */
    public function getcode($data) {
        $sql = $this->userEmail($data);
        $num = DI()->notorm->user_code->select('*')->where(array('id = ?'=>$sql['id'],'difference = ?'=>$data['num']))->fetch();
        return $num;
    }

/*
 * 重置密码
 */
    public function RePsw($psw,$data){
        $sql = $this->userEmail($data);
        $sqla =DI()->notorm->user_base->where('id = ?',$sql['id'])->update($psw);
    }

/*
 * 获得用户邮箱的校验状态
 */
    public function getMailChecked($user_id)
    {
        $sql =DI()->notorm->user_detail->select('user_base_id','mailChecked')->where('user_base_id = ?',$user_id)->fetch();
        $data['userID'] = $sql['user_base_id'];
        $data['mailChecked'] = $sql['mailChecked'];
        return $data;
    }

/*
 * 将处理加入私密星球的申请的结果保存到数据库
 */
    public function processAppInfo($field){
            $sql = DI()->notorm->message_detail->insert($field);
            if($sql){
                $rs = 1;
            }else{
                $rs = 0;
        }
        return $rs;
    }

/*
 * 从数据库中查找用户的消息列表并返回“私密星球申请”消息类型
 */
    public function ShowMessage($data,$page_num) {
        if($data['status']==1){
            $status=array(0,1,2,3,4);
        }elseif($data['status']==2){
            $status=array(2,3,4);
        }elseif($data['status']==3){
            $status=array(0,1);
        }
        $sql = DI()->notorm->message_detail
        ->select('*')
        ->where('user_base_id = ?',$data['user_id'])
        ->where('message_base_code',array('0001','0002','0003'))
        ->where('status',$status)
        ->order('createTime DESC')
        ->limit(($data['pn']-1)*$page_num,$page_num)
        ->fetchAll();
        return $sql;
        }
/*
 * 从数据库中查找用户的消息列表并返回“回复我的”消息类型
 */
    public function ShowReplyMessage($data,$page_num) {
        $sql='SELECT ub.id AS userID,md.message_id AS m_id,mt.text AS replyfloor,ub.nickname,pd.text AS replytext,pd.createTime,pb.title AS posttitle,pb.id AS postID ,gb.id AS groupID ,gb.name AS groupname FROM user_base ub,post_detail pd,post_base pb,group_base gb,message_detail md,message_text mt '
            ."WHERE md.user_base_id = :user_id AND md.message_base_code = '0007' AND md.id_1 =ub.id AND md.message_id =mt.message_detail_id AND pd.post_base_id = md.id_2 AND pd.floor =mt.text AND pd.post_base_id = pb.id AND gb.id = pb.group_base_id "
            .'AND md.status = 1 '
            .'ORDER BY pd.createTime DESC '
            .'LIMIT :limit_st,:page_num';
        $params = array(':user_id' => $data['user_id'], ':limit_st' => ($data['pn']-1)*$page_num, ':page_num' => $page_num);
        $re=DI()->notorm->message_detail->queryAll($sql, $params);
		foreach($re as $key=>$value){
			$model_p = new Model_Post();
			$value['page'] = $model_p->getPostReplyPage($value['postID'],$value['replyfloor']);
			$re[$key] = $value;
		}
        return $re;
    }
/*
 * 从数据库中查找用户的消息列表并返回“私密星球申请”消息类型
 */
    public function showAnotherMessage($data,$page_num) {
        $sql = DI()->notorm->message_detail
        ->select('*')
        ->where('user_base_id = ?',$data['user_id'])
        ->where('message_base_code',array('0004','0005','0006'))
        ->order('createTime DESC')
        ->limit(($data['pn']-1)*$page_num,$page_num)
        ->fetchAll();
        return $sql;
    }
/*
 * 通过用户id查找所有的消息“回复我的”消息类型(为了得到消息的个数)
 */
    public function getAllReplyMessage($user_id){
        $sql = DI()->notorm->message_detail
        ->select('*')
        ->where('user_base_id = ?',$user_id)
        ->where('message_base_code','0007')
        ->where('status',1)
        ->fetchAll();
        return $sql;
    }
/*
 * 通过用户id查找所有的消息“其他通知”消息类型(为了得到消息的个数)
 */
    public function getAllAnotherMessage($user_id){
        $sql = DI()->notorm->message_detail
        ->select('*')
        ->where('user_base_id = ?',$user_id)
        ->where('message_base_code',array('0004','0005','0006'))
        ->fetchAll();
        return $sql;
    }
/*
 * 通过用户id查找所有的消息“私密星球申请”消息类型(为了得到消息的个数)
 */
    public function getAllMessage($user_id,$status){
        if($status==1){
            $status=array(0,1,2,3,4);
        }elseif($status==2){
            $status=array(2,3,4);
        }elseif($status==3){
            $status=array(0,1);
        }
        $sql = DI()->notorm->message_detail
        ->select('*')
        ->where('user_base_id = ?',$user_id)
        ->where('message_base_code',array('0001','0002','0003'))
        ->where('status',$status)
        ->fetchAll();
        return $sql;
    }
/*
 * 找出对应的消息类型并返回
 */
    public function getCorrespondInfo($message_base_code) {
        $sql = DI()->notorm->message_base->select('content,type')->where('code = ?',$message_base_code)->fetch();
        return $sql;
    }

/*
 * 通过星球id查找星球名字
 */
    public function getGroupName($id) {
        $sql = DI()->notorm->group_base->select('name')->where('id = ?',$id)->fetch();
        return $sql['name'];
    }
/*
 * 通过用户id查找用户名字
 */
    public function getUserName($id) {
        $sql = DI()->notorm->user_base->select('nickname')->where('id = ?',$id)->fetch();
        return $sql['nickname'];
    }
/*
 * 用于将所有未读消息标记为已读
 */
    public function alterStatus($data,$status) {
        $field['status'] = $status;
        if(empty($data['message_id'])){
        $rs = DI()->notorm->message_detail
            ->where('user_base_id = ? AND status = ?',$data['user_base_id'],$data['status'])
        ->update($field);
        }else{
            $rs = DI()->notorm->message_detail
            ->where('message_id = ? AND user_base_id = ? AND status = ? AND message_base_code = ?',
            $data['message_id'],$data['user_base_id'],$data['status'],$data['message_base_code'])
            ->update($field);
        }
        return $rs;
    }
/*
 * 通过用户id查找用户的未读信息条数
 */
    public function CheckNewInfo($id) {
        $sql = DI()->notorm->message_detail->select('status')->where('user_base_id = ? AND status = ?',$id,0)->fetchAll();
        return $sql;
    }

/*
 * 通过消息id查找消息详情
 */
    public function getMessageInfo($message_id){
        $sql = DI()->notorm->message_detail->select('*')->where('message_id = ?',$message_id)->fetchone();
        return $sql;
    }
/*
 * 通过消息id查找申请信息、回复楼层
 */
    public function getMessageText($message_id){
        $sql = DI()->notorm->message_text->select('text')->where('message_detail_id = ?',$message_id)->fetchone();
        return $sql['text'];
    }
/*
 * 用于删除回复我的消息类型中帖子回复已被删除的消息
 */
    public function deleteMessage($data){
        $field['status'] = 2;
        $rs = DI()->notorm->message_detail->where('message_id = ?',$data['m_id'])->update($field);
        return $rs;
    }
    public function changepwd($data){
        $field['password'] = md5($data['newpwd']);
        $rs = DI()->notorm->user_base->where('id = ?',$data['user_id'])->update($field);
    }
}
