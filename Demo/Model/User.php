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

    public function getUserInfo($user_id){
        $sql=DI()->notorm->user_detail->select('user_base_id as userID','sex','year','month','day','mailChecked')->where('user_base_id=?',$user_id)->fetch();
        $sqlb=DI()->notorm->user_base->select('Email','nickname')->where('id=?',$user_id)->fetch();
        $sql['Email']=$sqlb['Email'];
        $sql['nickname']=$sqlb['nickname'];
        return $sql;
    }

    public function alterUserInfo($user_id,$sex,$year,$month,$day){
        $data=array('sex'=>$sex,
            'year'=>$year,
            'month'=>$month,
            'day'=>$day);
        $sql=DI()->notorm->user_detail->where('user_base_id=?',$user_id)->update($data);
        if(isset($sql)){
            $re['data']=1;
            $re['msg']='修改成功';
        }else{
            $re['data']=0;
            $re['msg']='修改失败';
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
 * 从数据库中查找用户的消息列表并返回
 */
    public function ShowMessage($data) {
        $sql = DI()->notorm->message_detail->select('*')->where('user_base_id = ?',$data['user_id'])->fetchAll();
        return $sql;
        }
/*
 * 找出对应的消息类型并返回
 */
    public function getCorrespondInfo($message_base_code) {
        $sql = DI()->notorm->message_base->select('content')->where('code = ?',$message_base_code)->fetch();
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
 * 用于将未读消息标记为已读
 */
    public function alterRead($data) {
        $saw['status'] = 1;
        $rs = DI()->notorm->message_detail
        ->where('user_base_id = ? AND message_base_code = ? AND count = ?',$data['user_id'],$data['message_code'],$data['countnum'])
        ->update($saw);
        return $rs;
    }
}
