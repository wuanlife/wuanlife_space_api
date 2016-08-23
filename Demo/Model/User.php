<?php

class Model_User extends PhalApi_Model_NotORM {
    protected function getTableName($id){
    return 'user_base';}
    public function userEmail($data)
    {
        $rs =DI()->notorm->user_base->select('*')->where('Email = ?',$data['Email'])->fetch();//通过邮箱查找用户相关信息
        return $rs;
               }
    public function usernickname($data)
               {
        $rs =DI()->notorm->user_base->select('*')->where('nickname = ?',$data['nickname'])->fetch();//通过昵称查找用户相关信息

        return $rs;
    }
    public function reg($data)
    {
                $data['password'] = md5($data['password']);
                $rs=DI()->notorm->user_base->insert($data);
					$data_detail = array(
						'user_base_id'   =>$rs['id'],
					);
				    $sql=DI()->notorm->user_detail->insert($data_detail);
        return $rs;
    }
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

	public function mailChecked($data,$mailChecked) {
		$row = $this->userEmail($data);
		$sql =DI()->notorm->user_detail->where('user_base_id = ?',$row['id'])->update($mailChecked);
		return $row;
	}
    public function SendMail($data){
		$data['Email'] = stripslashes(trim($data['Email']));
		$domain = new Domain_User();
        $data['Email'] = $domain->injectChk($data['Email']);
        $rs = $this->userEmail($data);
        return $rs;
    }
    

	public function updatecode($code,$data){
		$sql = $this->userEmail($data);
		$row = DI()->notorm->user_base->where('id = ?',$sql['id'])->update($code);
		return $sql;
	}

    public function getMailChecked($user_id)
    {
        $sql =DI()->notorm->user_detail->select('user_base_id','mailChecked')->where('user_base_id = ?',$user_id)->fetch();
        $data['userID'] = $sql['user_base_id'];
        $data['mailChecked'] = $sql['mailChecked'];
        return $data;
    }
}
