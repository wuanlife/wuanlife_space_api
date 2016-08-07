<?php

class Model_User extends PhalApi_Model_NotORM {
    protected function getTableName($id){
    return 'user_base';}
    public function login($data)
    {
        $sql =DI()->notorm->user_base->select('id')->where('Email = ?',$data['Email'])->fetch();
        if(empty($sql))
        {
        $this->code = '0';
        $this->msg = '该邮箱尚未注册！';
        }
        else{
               $rs =DI()->notorm->user_base->select('*')->where('Email = ?',$data['Email'])->fetch();
               if($rs['password']!=md5($data['password']))
               {
               $this->code = '0';
               $this->msg = '密码错误，请重试！';
               }
               else
               {
                $this->info = array('userID' => $rs['id'], 'nickname' => $rs['nickname'], 'Email' => $rs['Email']);
                $this->code ='1';
                $this->msg = '登录成功！';
                /*
                $config = array('crypt' => new Domain_Crypt(), 'key' => 'a secrect');
                DI()->cookie = new PhalApi_Cookie_Multi($config);
                $nickname = DI()->cookie->get('nickname');
                DI()->cookie->set('nickname', $rs['nickname'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
                $userID = DI()->cookie->get('userID');
                DI()->cookie->set('userID', $rs['id'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
                $Email = DI()->cookie->get('Email');
                DI()->cookie->set('Email', $rs['Email'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
                */
                }


            }
        return $this;
    }
    public function reg($data)
    {
        $sql =DI()->notorm->user_base->select('id')->where('Email = ?',$data['Email'])->fetch();
        if(!empty($sql)){
        $this->code = '0';
        $this->msg = '该邮箱已注册！';
        }
        else{
               $sql =DI()->notorm->user_base->select('id')->where('nickname = ?',$data['nickname'])->fetch();
				if(!empty($sql)) {
               $this->code = '0';
               $this->msg = '该昵称已注册！';
               }else {
                $data['password'] = md5($data['password']);
                $rs=DI()->notorm->user_base->insert($data);
					$data_detail = array(
						'user_base_id'   =>$rs['id'],
					);
				    $sql=DI()->notorm->user_detail->insert($data_detail);
                $this->info = array('userID' => $rs['id'], 'nickname' => $rs['nickname'], 'Email' => $rs['Email']);
                $this->code ='1';
                $this->msg = '注册成功，并自动登录！';
                /*
                $config = array('crypt' => new Domain_Crypt(), 'key' => 'a secrect');
                DI()->cookie = new PhalApi_Cookie_Multi($config);
                $nickname = DI()->cookie->get('nickname');
                DI()->cookie->set('nickname', $rs['nickname'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
                $userID = DI()->cookie->get('userID');
                DI()->cookie->set('userID', $rs['id'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
                $Email = DI()->cookie->get('Email');
                DI()->cookie->set('Email', $rs['Email'], $_SERVER['REQUEST_TIME'] + 3600 * 24 * 7 * 2);
                */
                }
            }
        return $this;
    }
    public function logout()
    {
        /*
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
        if($sql){
            $re=1;
        }else{
            $re=0;
        }
        return $re;
    }
	public function CheckMail($data) {
		@session_start();
		if($data['code'] == '') {
			if(empty($_SESSION['E_code'])) {
			$data['Email'] = stripslashes(trim($data['Email']));
			$info = $this->SendMail($data);
			//$info = "未输入验证码";
			}else {
				$info = "请输入验证码";
			}
		}else {
			$info = $this->mailChecked($data);
		}
		return $info;
	}
	public function mailChecked($data) {
		$num = 0;
		$code = $data['code'];
		$Email = $data['Email'];
		$row =DI()->notorm->user_base->select('getpasstime','id')->where('Email = ?',$Email)->fetch();
		if(time()-$row['getpasstime']>1*10*60){
			//$this->code = 1;
			$this->msg = '验证码已过期，请重新获取！';
			@session_start();
			unset($_SESSION['E_code']);
		}else {
			@session_start();
			if(empty($_SESSION['E_code'])) {
				$this->msg = '验证码已失效，请重新获取！';
			}else {
				if($code == $_SESSION['E_code']) {
					$data = array('mailChecked'=>1);
					$sql =DI()->notorm->user_detail->where('user_base_id = ?',$row['id'])->update($data);
					unset($_SESSION['E_code']);
					$num = 1;
					$this->code = 1;
					$this->msg = '您的邮箱验证成功！';
				}else {
					$this->msg = '验证码不正确，请确认！';
				}
		    }
		}
		if(!$num) {
			$this->code = 0;
		}
		return $this;
	}
    public function SendMail($data){
        $email = stripslashes(trim($data['Email']));
        $email = $this->injectChk($email);
        $sql =DI()->notorm->user_base->select('id,nickname,password')->where('email = ?',$email)->fetch();
        if(empty($sql)){//该邮箱尚未注册！
            $this->code = 0;
            $this->msg = '您输入的账号不存在！';  
        }else{
            $getpasstime = time();
            $uid = $sql['id'];
            //$token = md5($uid.$sql['nickname'].$sql['password']);
            //$url = "http://localhost/mail/reset.php?email=".$email."&token=".$token;
            $time = date('Y-m-d H:i');
            require_once '././init.php';
            DI()->loader->addDirs('Library');
            $mailer = new PHPMailer_Lite(true);
            $recipients = $email;
			$code = $this->code();
			if($data['num']) {
				$info = "验证邮箱";
				$title = "午安网 - 验证邮箱";
				@session_start();
				$_SESSION['E_code'] = $code;//将获得的码值字符保存到session中
			}else {
				$info = "找回密码";
				$title = "午安网 - 密码找回";
				@session_start();
		        $_SESSION['P_code'] = $code;//将获得的码值字符保存到session中
			}
            $body = "亲爱的".$email."：<br/>您在".$time."提交了".$info."请求。<br/>您的验证码为  ".$code."，有效期十分钟！";
			//请点击下面的链接重置密码（链接1小时内有效）。<br/><a href='".$url."' target='_blank'>".$url."</a>
            $rs = $mailer->send("$recipients","$title","$body");
                if($rs){
                    $this->code = 1;
                    $this->msg = '系统已向您的邮箱发送了一封'.$info.'邮件，请登录到您的邮箱查看验证码！';
                    //更新数据发送时间
                    $data = array('getpasstime'=>$getpasstime);
                    $sqla =DI()->notorm->user_base->where('id = ?',$uid)->update($data);
                }
                else{
                    $this->code = 0;
                    $this->msg='发送邮件失败，请重试！';
                }
        }
        return $this;
    }
    
    public function injectChk($sql_str) { //防止注入
        /*php5.3起不再支持eregi()函数
		相关链接http://www.t086.com/article/5086
		*/
		//$check = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
        $check = preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i', $sql_str);
        if ($check) {
            echo('您的邮箱格式包含非法字符，请确认！');
            exit ();
        } else {
            return $sql_str;
        }
    }
	public function code() { //生成5位数字验证码
		$char_len = 5;
		//$font = 6;
		$char = array_merge(/*range('A','Z'),range('a','z'),*/range('0','9'));//生成随机码值数组，不需要0，避免与O冲突
		$rand_keys = array_rand($char,$char_len);//随机生成$char_len个码值的键；
		if($char_len == 1) {//判断码值长度为一时，将其放入数组中
			$rand_keys = array($rand_keys);
		}
		shuffle($rand_keys);//打乱数组
		$code = '';
		foreach($rand_keys as $key) {
			$code .= $char[$key];
		}//拼接字符串
		//@session_start();
		//$_SESSION['captcha_code'] = $code;//将获得的码值字符保存到session中
		return $code;
	}
	public function RePsw($data) {
		$num = 0;
		$code = $data['code'];
		$password = $data['password'];
		$psw = $data['psw'];
		$Email = $data['Email'];
		$row =DI()->notorm->user_base->select('getpasstime')->where('Email = ?',$Email)->fetch();
		if(time()-$row['getpasstime']>1*10*60){
			//$this->code = 1;
			$this->msg = '验证码已过期，请重新获取！';
			@session_start();
			unset($_SESSION['P_code']);
		}else {
			@session_start();
			if(empty($_SESSION['P_code'])) {
				$this->msg = '验证码已失效，请重新获取！';
			}else {
				if($code == $_SESSION['P_code']) {
					if($password == $psw) {
						$num = 1;
						$data = array('password'=>md5($password));
						$sql =DI()->notorm->user_base->where('Email = ?',$Email)->update($data);
						unset($_SESSION['P_code']);
						$this->msg = '密码修改成功！';
					}else {
						$this->msg = '两次密码不一致，请确认！';
					}
				}else {
					$this->msg = '验证码不正确，请确认！';
				}
			}
		}
		if(!$num) {
			$this->code = 0;
		}
		return $this;
	}

    public function getMailChecked($user_id)
    {
        $sql =DI()->notorm->user_detail->select('user_base_id','mailChecked')->where('user_base_id = ?',$user_id)->fetch();
        $data['userID'] = $sql['user_base_id'];
        $data['mailChecked'] = $sql['mailChecked'];
        return $data;
    }
}
