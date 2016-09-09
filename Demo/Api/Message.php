<?php
/**
 * 消息中心服务类
 */

class Api_Message extends PhalApi_Api{

    public function getRules(){
        return array(
            'PrivateGroup' => array(
                'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),

                'group_id' => array(
                    'name'    => 'group_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '星球ID'
                ),
            ),
			'AgreeApp'     => array(
				'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),

                'group_id' => array(
                    'name'    => 'group_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '星球ID'
                ),
				
				'applicant_id' =>array(
                    'name'    => 'applicant_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '申请人ID'
                ),
			
			),
			'DisagreeApp'     => array(
				'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),

                'group_id' => array(
                    'name'    => 'group_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '星球ID'
                ),
				
				'applicant_id' =>array(
                    'name'    => 'applicant_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '申请人ID'
                ),
			
			),
			'ShowMessage'		  =>array(
				'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'min'     => '1',
                    'require' => true,
                    'desc'    => '用户ID'
                ),			
			),
		);
	}
	
/**
 * 私密星球申请加入接口
 * @desc 用于申请者加入私密星球
 * @return int code 操作码，1表示申请成功，0表示申请失败
 * @return string msg 提示信息
 */
	public function PrivateGroup(){
		$data = array(
            'user_id'    => $this->user_id,
            'group_id' => $this->group_id,
            );
		$domain = new Domain_Message();
        $rs = $domain->PrivateGroup($data);
        return $rs;
	}
	
/**
 * 同意申请者申请加入私密星球接口
 * @desc 用于同意申请加入私密星球
 * @return int code 操作码，1表示操作成功，0表示操作失败
 * @return string msg 提示信息
 */
	public function AgreeApp(){
		$data = array(
            'user_id'    	=> $this->user_id,
            'group_id' 		=> $this->group_id,
			'applicant_id'	=> $this->applicant_id,
            );
		$domain = new Domain_Message();
        $rs = $domain->AgreeApp($data);
        return $rs;
	}
	
/**
 * 拒绝申请者加入私密星球接口
 * @desc 用于拒绝申请者加入私密星球
 * @return int code 操作码，1表示操作成功，0表示操作失败
 * @return string msg 提示信息
 */
	public function DisagreeApp(){
		$data = array(
            'user_id'    	=> $this->user_id,
            'group_id' 		=> $this->group_id,
			'applicant_id'	=> $this->applicant_id,
            );
		$domain = new Domain_Message();
        $rs = $domain->DisagreeApp($data);
        return $rs;
	}

/**
 * 用户消息中心接口
 * @desc 用于接收其他用户发送给用户消息
 * @return int code 操作码，1表示接收成功，0表示接收失败
 * @return string msg 提示信息
 */
	public function ShowMessage(){
		$data = array(
            'user_id'    	=> $this->user_id,
            );
		$domain = new Domain_Message();
        $rs = $domain->ShowMessage($data);
        return $rs;
	}
}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	