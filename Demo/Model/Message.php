<?php

class Model_Message extends PhalApi_Model_NotORM {
    protected function getTableName($id){
    return 'message_detail';}
	
/*
 * 插入私密星球相关信息到数据库
 */
	public function PrivateGroup($data) {
		$founder_id = $this->getCreatorId($data); //通过星球id查找创建者id
		$field = array(
					'message_base_id'   =>'0001',
					'user_base_id'      =>$founder_id,
					'id_1'				=>$data['user_id'],
					'id_2'              =>$data['group_id'],
					'createTime'      	=>time(),
		);
		$sql = DI()->notorm->message_detail->insert($field);
		if($sql){
			$rs = 1;
		}else{
			$rs = 0;
		}
		return $rs;
	}
	
/*
 * 通过星球id查找创建者id
 */
	public function getCreatorId($data) {
		$sql=DI()->notorm->group_detail
		->select('user_base_id')
		->where('group_base_id= ?',$data['group_id'])
		->where('authorization= ?',01)
		->fetch();
		return $sql['user_base_id'];
	}
	
/*
 * 同意加入私密星球的申请并修改数据库相关字段信息
 */
	public function AgreeApp($data) {
		$founder_id = $this->getCreatorId($data); //通过星球id查找创建者id
		$Boolean = ($data['user_id'] == $founder_id); //判断是否为创建者
		if($Boolean){
			$field = array(
					'group_base_id'   =>$data['group_id'],
					'user_base_id'    =>$data['applicant_id'],
					'authorization'   =>03,
			);
			$sql = DI()->notorm->group_detail->insert($field);
			if($sql){
				$rs = 1;
			}else{
				$rs = 0;
			}
			$field = array(
					'message_base_id'   =>'0002',
					'user_base_id'      =>$founder_id,
					'id_1'				=>$data['user_id'],
					'id_2'              =>$data['group_id'],
					'createTime'      	=>time(),
			);
			$sql = DI()->notorm->message_detail->insert($field);
		}else{
			return 2;exit;
		}
		return $rs;
	}
/*
 * 拒绝加入私密星球的申请并修改数据库相关字段信息
 */
	public function DisagreeApp($data) {
		$founder_id = $this->getCreatorId($data); //通过星球id查找创建者id
		$Boolean = ($data['user_id'] == $founder_id); //判断是否为创建者
		if($Boolean){
			$field = array(
					'message_base_id'   =>'0003',
					'user_base_id'      =>$founder_id,
					'id_1'				=>$data['user_id'],
					'id_2'              =>$data['group_id'],
					'createTime'      	=>time(),
			);
			$sql = DI()->notorm->message_detail->insert($field);
			if($sql){
				$rs = 1;
			}else{
				$rs = 0;
			}
		}else{
			return 2;exit;
		}
		return $rs;
	}
/*
 * 从数据库中查找用户的消息并返回
 */
	public function ShowMessage($data) {
		$sql = DI()->notorm->message_detail->select('*')->where('user_base_id = ?',$data['user_id'])->fetchAll();
		$sql_1 = DI()->notorm->message_base->select('*')->where('code = ?',$sql[0]['message_base_id'])->fetchAll();
		foreach($sql as $keys => $value){
			$sql_1 = DI()->notorm->message_base->select('content')->where('code = ?',$value['message_base_id'])->fetch();
			$group_name = $this->getGroupName($value['id_2']);
			$user_name = $this->getUserName($value['id_1']);
			$OldCharacter = array("{0}","{1}");
			$NewCharacter = array("$user_name","$group_name");
			$sql_1['content'] = str_replace($OldCharacter,$NewCharacter,$sql_1['content']);
			$sql[$keys]['message_base_id'] = $sql_1['content'];
		}
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
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}