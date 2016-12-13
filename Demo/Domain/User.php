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
 * 通过消息id查找消息详情
 */
    public function getMessageInfo($message_id){
        $model_u = new Model_User;
        $rs = $model_u->getMessageInfo($message_id);
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

    public function alterUserInfo($id,$data){
        $model=new Model_User();
        $rs=$model->alterUserInfo($id,$data);
        return $rs;
    }
/*
 * 发送包含修改密码验证码的邮件
 */

    public function SendMail($data){
        $model = new Model_User();
        $sql = $model->SendMail($data);
        $this->code = 0;
        if(empty($sql)){
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
            if($data['num'] == 2) {
                $info = "验证邮箱";
                $title = "午安网 - 验证邮箱";
                $code_e = array('code' => $code,'difference' => 2,'getpasstime'=>time(),'used'=>0,);
                $model->updatecode($code_e,$data);
            }else {
                $info = "找回密码";
                $title = "午安网 - 密码找回";
                $code_e = array('code' => $code,'difference' => 1,'getpasstime'=>time(),'used'=>0,);
                $model->updatecode($code_e,$data);
            }
            $body = "亲爱的".$data['Email']."：<br/>您在".$time."提交了".$info."请求。<br/>您的验证码为  ".$code."，有效期十分钟！";
            $rs = $mailer->send("$recipients","$title","$body");
                if($rs){
                    $this->code = 1;
                    $this->msg = '系统已向您的邮箱发送了一封'.$info.'邮件，请登录到您的邮箱查看验证码！';
                    //更新验证码有效期
                    //$code_time = array('getpasstime'=>time());
                    //$model->updatecode($code_time,$data);
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
    public function CheckMail($data){
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
        $row =$model->userEmail($data);
        if(!$row) {
            $this->msg = '用户名不存在，请确认！';
            return $this;
            exit();
        }
        $row =$model->getcode($data);
        $Boolean = time()-$row['getpasstime']>1*10*60;
        if($Boolean) {
            $this->msg = '验证码已过期，请重新获取！';
        }else {
            if($row['used']==1) {
                $this->msg = '验证码已失效，请重新获取！';
            }else {
                if($data['code'] == $row['code']) {
                    if($data['password'] == $data['psw']) {
                        $psw = array('password'=>md5($data['password']));
                        $code_p = array('used'=>1);
                        $model->RePsw($psw,$data);
                        $model->updatecode($code_p,$data);
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
        return $this;
    }
/*
 * 校验验证码并验证邮箱
 */
    public function mailChecked($data){
        $model = new Model_User();
        $this->code = 0;
        $row =$model->userEmail($data);
        if(!$row) {
            $this->msg = '用户名不存在，请确认！';
            return $this;
            exit();
        }
        $row =$model->getcode($data);
        $Boolean = time()-$row['getpasstime']>1*10*60;
        if($Boolean){
            $this->msg = '验证码已过期，请重新获取！';
        }else {
            if($row['used']==1) {
                $this->msg = '验证码已失效，请重新获取！';
            }else {
                if($data['code'] == $row['code']) {
                    $mailChecked = array('mailChecked'=>1);
                    $model->mailChecked($data,$mailChecked);
                    $code_e = array('used' => 1);
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

/*
 * 用于处理加入私密星球的申请
 */
    public function ProcessApp($data){
        $this->code = 0;
        $info = $this->getMessageInfo($data['message_id']);
        $model_g = new Model_Group();
        $data['group_id'] = $info['id_2'];
        $data['applicant_id'] = $info['id_1'];
        $founder_id = $model_g->getCreatorId($data); //通过星球id查找创建者id
        $Boolean = ($data['user_id'] == $founder_id); //判断是否为创建者
        if($Boolean){
            $Boolean = $model_g->checkGroup($data['applicant_id'],$data['group_id']);//判断用户是否已经加入私密星球
            if(empty($Boolean)){
            if($data['mark'] == 1) {
                $field = array(
                'group_base_id' => $data['group_id'],
                'user_base_id'  => $data['applicant_id'],
                'authorization' => "03",
                );
                $message_base_code = '0002';
                $sql = $model_g->join($field);//将用户id加入对应的私密星球
            }else{
                $message_base_code = '0003';
            }
                    $rs = $this->processAppInfo($message_base_code,$data);//加入私密星球的申请的结果返回给申请者
            $this->code = 1;
            if($data['mark'] == 1) {
                $this->msg = '操作成功！您已同意该成员的申请！';
                    $status = 2;
            }else {
                $this->msg = '操作成功！您已拒绝该成员的申请！';
                    $status = 3;
            }
                $field['message_base_code'] = '0001';
                $field['message_id'] = $data['message_id'];
                $field['user_base_id'] = $info['user_base_id'];
                $field['status'] = 1;
                $this->alterStatus($field,$status);//将消息列表已读转化为处理之后的标记(已同意或者已拒绝)
            }else{
                $this->msg = '操作失败！该用户已加入此星球！';
            }
        }else{
            $this->msg = '您不是创建者，没有权限！';
        }
        return $this;
    }
/*
 * 加入私密星球的申请的结果返回给申请者
 */
    public function processAppInfo($message_base_code,$data){
        $model_u = new Model_User();
        /*
        $model_g = new Model_Group();
        $maxcount = $model_g->getMaxCount($message_base_code,$data['applicant_id']);//获得消息列表最大count
        */
        $field = array(
            'message_base_code' =>$message_base_code,
            'user_base_id'      =>$data['applicant_id'],
            /*
            'count'             =>$maxcount+1,
            */
            'id_1'              =>$data['user_id'],
            'id_2'              =>$data['group_id'],
            'createTime'        =>time(),
        );
        $sql = $model_u->processAppInfo($field);
        return $sql;
    }
/*
 * 组合用户的消息
 */
    public function ComposeInfo($group_name,$user_name,$content){
        $OldCharacter = array("{0}","{1}");
        $NewCharacter = array("$user_name","$group_name");
        $content = str_replace($OldCharacter,$NewCharacter,$content);
        return $content;
    }
/*
 * 用于显示用户的消息列表的“回复我的”消息类型
 */
    public function getReplyMessage($data){
        $model = new Model_User();
        $model_p = new Model_Post();
        $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
        $status = 1;//消息已读
        $this->alterStatus($value,$status);//将消息列表转化为已读
        $num = $model->getAllReplyMessage($data['user_id']);
        $page_num = 20;
        $pageCount  = ceil(count($num)/$page_num);
        if($data['pn'] > $pageCount){
            $data['pn'] = $pageCount;
        }
        $rs = array();
        if($data['pn'] !=0){
            $rs = $model->ShowReplyMessage($data,$page_num);
        }
        if($rs){
            $this->code = 1;
            $this->info = $rs;
            $this->pageCount  = $pageCount;
            $this->currentPage  = $data['pn'];
            $this->msg  = '接收成功';
        }else{
            $this->code = 0;
            $this->msg  = '您暂时没有消息！';
        }
        return $this;
    }
/*
 * 用于显示用户的消息列表的“其他通知”消息类型
 */
    public function getAnotherMessage($data){
        $model = new Model_User();
        $model_p = new Model_Post();
        $num = $model->getAllAnotherMessage($data['user_id']);
        $page_num = 20;
        $pageCount  = ceil(count($num)/$page_num);
        if($data['pn'] > $pageCount){
            $data['pn'] = $pageCount;
        }
        $rs = array();
        if($data['pn'] !=0){
            $rs = $model->showAnotherMessage($data,$page_num);
            foreach($rs as $keys => $value){
                $sql = $model->getCorrespondInfo($value['message_base_code']);
                $group_name = $model->getGroupName($value['id_2']);//通过星球id查找星球名字
                $user_name = $model->getUserName($value['id_1']);//通过用户id查找用户名字
                $sql['content'] = $this->ComposeInfo($group_name,$user_name,$sql['content']);
                $rs[$keys] = array(
                                'id'            =>$value['message_id'],
                                'nickname'      =>$user_name,
                                'messageInfo'   =>$sql['content'],
                                'group_id'      =>$value['id_2'],
                                'createTime'    =>date('Y-m-d H:i',$value['createTime']),
                );
            }
        }
        if($rs){
            $this->code = 1;
            $this->info = $rs;
            $this->pageCount  = $pageCount;
            $this->currentPage  = $data['pn'];
            $this->msg  = '接收成功';
        }else{
            $this->code = 0;
            $this->msg  = '您暂时没有消息！';
        }
        return $this;
    }
/*
 * 用于显示用户的消息列表的“私密星球申请”消息类型
 */
    public function ShowMessage($data){
    if($data['messageType']==1){
        return $this->getReplyMessage($data);
    }elseif($data['messageType']==2){
        return $this->getAnotherMessage($data);
    }elseif($data['messageType']==3){
        $model = new Model_User();
        $num = $model->getAllMessage($data['user_id'],$data['status']);
            $value['status'] = 0;
        $value['user_base_id'] = $data['user_id'];
            $status = 1;//消息已读
        $this->alterStatus($value,$status);//将消息列表转化为已读
        $page_num = 20;
		$pageCount  = ceil(count($num)/$page_num);
		if($data['pn'] > $pageCount){
			$data['pn'] = $pageCount;
		}
		$rs = array();
		if($data['pn'] !=0){
        $rs = $model->ShowMessage($data,$page_num);
        foreach($rs as $keys => $value){
            $sql = $model->getCorrespondInfo($value['message_base_code']);
            $group_name = $model->getGroupName($value['id_2']);//通过星球id查找星球名字
            $user_name = $model->getUserName($value['id_1']);//通过用户id查找用户名字
            $user = $model->getUserInfo($value['id_1']);//通过用户id查找用户资料（用户头像）
            $sql['content'] = $this->ComposeInfo($group_name,$user_name,$sql['content']);
			$a=null;
			if($value['message_base_code']=='0002'){
				$a='同意';
			}elseif($value['message_base_code']=='0003'){
				$a='拒绝';
			}
            $rs[$keys] = array(
                'id'            =>$value['message_id'],
                'user_image'    =>$user['profile_picture'],
                'nickname'      =>$user_name,
                'messagetype'   =>$sql['type'],
                'messageInfo'   =>array(
                'information'   =>'已'.$a.'你的加入',
                'group_name'    =>$group_name,
                'group_id'      =>$value['id_2'],
                ),
                'createTime'    =>date('Y-m-d H:i',$value['createTime']),

            );
            if($data['status']==3){
                $value['status'] = 1;
                $value['user_base_id'] = $data['user_id'];
                $status = 4;//消息已读
                $value['message_base_code'] = '0002';
                $this->alterStatus($value,$status);//将消息列表转化为已读
                $value['message_base_code'] = '0003';
                $this->alterStatus($value,$status);//将消息列表转化为已读
            }
            if($sql['type'] == '1'){
                $text = $model->getMessageText($value['message_id']);
                $rs[$keys] = array(
                    'id'            =>$value['message_id'],
                    'user_image'    =>$user['profile_picture'],
                    'nickname'      =>$user_name,
                    'messagetype'   =>$sql['type'],
                    'messageInfo'   =>array(
                    'information'   =>'申请加入',
                    'group_name'    =>$group_name,
                    'group_id'      =>$value['id_2'],
                    'status'        =>$value['status'],
                    'text'          =>$text,
                    ),
                    'createTime'    =>date('Y-m-d H:i',$value['createTime']),
                );
            }
        }
		}
        if($rs) {
            $this->code = 1;
            $this->info = $rs;
            $this->pageCount  = $pageCount;
            $this->currentPage  = $data['pn'];
            $this->msg  = '接收成功';
        }else{
            $this->code = 0;
            $this->msg  = '您暂时没有消息！';
			/*
            if(ceil(count($num)/$page_num) !=0 && $data['pn'] >ceil(count($num)/$page_num)){
                    throw new PhalApi_Exception_BadRequest('页面不存在！');
            }
			*/
        }
        return $this;
    }
    }
/*
 * 用于将未读消息标记为已读或者其他标记
 */
    public function alterStatus($data,$status){
        $model = new Model_User();
        $rs = $model->alterStatus($data,$status);
        /*
        if($rs){
            $this->code = 1;
            //$this->info = $rs;
            $this->msg = '操作成功！';
        }else{
            $this->code = 0;
            //$this->info = $rs;
            $this->msg = '操作失败！';
        }
        return $this;
        */
    }
/*
 * 返回用户是否有未读信息
 */
    public function CheckNewInfo($data) {
        $model = new Model_User();
        $user_id = $data['user_id'];
        $rs = $model->CheckNewInfo($user_id);
        if($rs) {
            $this->num = 1;
        }else{
            $this->num = 0;
        }
        return $this;
    }
/*
 * 用于删除回复我的消息类型中帖子回复已被删除的消息
 */
    public function deleteMessage($data){
        $model = new Model_User();
        $rs = $model->deleteMessage($data);
        if($rs) {
            $this->code = 1;
            $this->msg = '删除成功';
        }else{
            $this->code = 0;
            $this->msg = '删除失败';
        }
        return $this;
    }
/*
 * 用于修改密码
 */
    public function changepwd($data){
        $model = new Model_User();
        $pwd = $model->userid($data['user_id']);
        $rs['code'] = 0;
        if($pwd['password']!=md5($data['pwd'])){
            $rs['msg'] = '登录密码不正确';
        }elseif($data['newpwd']!=$data['checkNewpwd']){
            $rs['msg'] = '两次密码不一致，请确认！';
        }else{
            $rs = $model->changepwd($data);
            $rs['msg'] = '修改成功！';
            $rs['code'] = 1;
        }
        return $rs;
    }
}
