<?php

class Domain_User {

    /*
    登录检查

    */

    public function login($data){
        $model = new Model_User();
        $rs = $model->userEmail($data);
		$this->code = '0';
		if(empty($rs)) {
			$this->msg = '该邮箱尚未注册！';
		}elseif($rs['password']!=md5($data['password'])){
				$this->msg = '密码错误，请重试！';
			}else{
				$this->info = array('userID' => $rs['id'], 'nickname' => $rs['nickname'], 'Email' => $rs['Email']);
				$this->code = '1';
				$this->msg = '登录成功！';
			}
        return $this;
    }
    /*
    注册检查

    */

    public function reg($data){
        $model = new Model_User();
        $Email = $model->userEmail($data);
		$nickname = $model->usernickname($data);
		$this->code = '0';
		if(!empty($Email)){
			$this->msg = '该邮箱已注册！';
		}elseif(!empty($nickname)){
			$this->msg = '该昵称已注册！';
			}else{
        $rs = $model->reg($data);
				$this->info = array('userID' => $rs['id'], 'nickname' => $rs['nickname'], 'Email' => $rs['Email']);
				$this->code ='1';
				$this->msg = '注册成功，并自动登录！';
			}
        return $this;
    }
    /*
	注销检查

    */
    public function logout(){
        //$model = new Model_User();
        //$rs = $model->logout();
		$this->code ='1';
        $this->msg = '注销成功！';
        return $this;
    }


/*
 * 判断用户是否为管理员
 */
    public function judgeAdmin($user_id){
        $model=new Model_User();
        $rs=$model->judgeAdmin($user_id);
        return $rs;
    }

/*
 * 判断用户是否为星球创建者
 */
    public function judgeCreate($user_id,$group_id){
        $model=new Model_User();
        $rs=$model->judgeCreate($user_id,$group_id);
        return $rs;
    }

    public function getUserInfo($user_id){
        $model=new Model_User();
        $rs=$model->getUserInfo($user_id);
        return $rs;
    }

    public function alterUserInfo($user_id,$sex,$year,$month,$day){
        $model=new Model_User();
        $rs=$model->alterUserInfo($user_id,$sex,$year,$month,$day);
        return $rs;
    }
/*
 * 发送包含修改密码验证码的邮件
 */

	public function SendMail($data){
        $model = new Model_User();
        $sql = $model->SendMail($data);
		$this->code = 0;
        if(empty($sql)){//该邮箱尚未注册！
            $this->msg = '您输入的账号不存在！';
        }else{
            //$getpasstime = time();
            $uid = $sql['id'];
            //$token = md5($uid.$sql['nickname'].$sql['password']);
            //$url = "http://localhost/mail/reset.php?email=".$email."&token=".$token;
            $time = date('Y-m-d H:i');
			$RootDIR = dirname(__FILE__);
			$path=$RootDIR."/../../Public/init.php";
            require_once $path;
            DI()->loader->addDirs('Library');
            $mailer = new PHPMailer_Lite(true);
            $recipients = $data['Email'];
			$code = $this->code();
			if($data['num']) {
				$info = "验证邮箱";
				$title = "午安网 - 验证邮箱";
                $code_e = array('e_code' => $code);
                $model->updatecode($code_e,$data);
			}else {
				$info = "找回密码";
				$title = "午安网 - 密码找回";
                $code_e = array('p_code' => $code);
                $model->updatecode($code_e,$data);
			}
            $body = "亲爱的".$data['Email']."：<br/>您在".$time."提交了".$info."请求。<br/>您的验证码为  ".$code."，有效期十分钟！";
            $rs = $mailer->send("$recipients","$title","$body");
                if($rs){
                    $this->code = 1;
                    $this->msg = '系统已向您的邮箱发送了一封'.$info.'邮件，请登录到您的邮箱查看验证码！';
                    //更新验证码有效期
                    $code_time = array('getpasstime'=>time());
                    $model->updatecode($code_time,$data);
                }
                else{
                    $this->msg='发送邮件失败，请联系系统管理员！';
                }
        }
        return $this;
    }	
/*
 * 发送包含验证邮箱验证码的邮件
 */
	public function CheckMail($data){   //在api层直接调用Domain的SendMail类还是不行，会返回133行的提示信息
		//$data['Email'] = stripslashes(trim($data['Email']));
		$info = $this->SendMail($data);
		return $info;
    }	
/*
 * 校验验证码并修改密码
 */

	public function RePsw($data){
        $model = new Model_User();
		$this->code = 0;
		$code = $data['code'];
		$password = $data['password'];
		$psw = $data['psw'];
		$Email = $data['Email'];
		$row =$model->userEmail($data);
		if(!$row) {
			$this->msg = '用户名不存在，请确认！';
			return $this;
			exit();
		}
        $Boolean = time()-$row['getpasstime']>1*10*60;
        if($Boolean) {
			$this->msg = '验证码已过期，请重新获取！';
            $code_p = array('p_code' => 1);
            //$this->bug = 
			$model->updatecode($code_p,$data);
		}else {
            $p_code=$model->userEmail($data);
            $Boolean = (int)$p_code['p_code']<9999;
            if($Boolean) {
				$this->msg = '验证码已失效，请重新获取！';
			}else {
                if($code == $p_code['p_code']) {
					if($password == $psw) {
						$code_p = array(
									'password'=>md5($password),
									'p_code' => 1,
								);
						$sql = $model->updatecode($code_p,$data);
                        $this->code = 1;
						$this->msg = '密码修改成功！';
					}else {
						$this->msg = '两次密码不一致，请确认！';
					}
				}else {
					$this->msg = '验证码不正确，请确认！';
				}
			}
		}
        //$rs = $model->RePsw($data);
        return $this;
    }
/*
 * 校验验证码并验证邮箱
 */
	public function mailChecked($data){
		$this->code = 0;
        $model = new Model_User();
		$row =$model->userEmail($data);
        $Boolean = time()-$row['getpasstime']>1*10*60;
        if($Boolean){
			//$this->code = 1;
			$this->msg = '验证码已过期，请重新获取！';
            $code_e = array('e_code' => 1);
            $model->updatecode($code_e,$data);
		}else {
            $e_code=$model->userEmail($data);
            $Boolean = (int)$e_code['e_code']<9999;
            if($Boolean) {
				$this->msg = '验证码已失效，请重新获取！';
			}else {
                if($data['code'] == $e_code['e_code']) {
					$mailChecked = array('mailChecked'=>1);
					$model->mailChecked($data,$mailChecked);
                    $code_e = array('e_code' => 1);
					$model->updatecode($code_e,$data);
					$this->code = 1;
					$this->msg = '您的邮箱验证成功！';
				}else {
					$this->msg = '验证码不正确，请确认！';
				}
		    }
		}
        return $this;
    }

/*
 * 验证用户邮箱是否已被验证
 */

    public function getMailChecked($user_id){
        $model = new Model_User();
        $rs = $model->getMailChecked($user_id);
        return $rs;
    }
/*
 * 防止注入
 */
    public function injectChk($sql_str) {
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
/*
 * 生成5位数字验证码
 */
	public function code() {
		$char_len = 5;
		//$font = 6;
        $char = array_merge(/*range('A','Z'),range('a','z'),*/range('1','9'));//生成随机码值数组，不需要0，避免与O冲突
		$rand_keys = array_rand($char,$char_len);//随机生成$char_len个码值的键；
		if($char_len == 1) {//判断码值长度为一时，将其放入数组中
			$rand_keys = array($rand_keys);
		}
		shuffle($rand_keys);//打乱数组
		$code = '';
		foreach($rand_keys as $key) {
			$code .= $char[$key];
		}//拼接字符串

		return $code;
	}
}
