<?php
/**
* 星球相关DB操作
*/
class Model_Group extends PhalApi_Model_NotORM{

	public function checkName($g_name){
		return $this->getORM()->select('id')->where('name = ?', $g_name)->fetchOne();
	}

	public function checkGroup($userID, $groupID){
		return DI()->notorm->group_detail->select('group_base_id')->where('user_base_id = ?', $userID)->where('group_base_id = ?', $groupID)->fetchOne();
	}

	public function getUser($user_id){
		$re=DI()->notorm->user_base->select('nickname')->where('id = ?', $user_id)->fetchOne();
		return $re['nickname'];
	}


	public function getAllNum(){
		return $this->getORM()->count('id');
	}

	public function lists($limit_st, $page_num){
		$sql='SELECT gb.name,gb.id,gb.g_image,gb.g_introduction,COUNT(gd.user_base_id) AS num FROM group_detail gd, group_base gb '
			.'where gb.id = gd.group_base_id '
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            .'LIMIT :limit_st,:page_num';
        $params = array(':limit_st' => $limit_st, ':page_num' => $page_num);
        $re = $this->getORM()->queryAll($sql, $params);
/*		foreach ($re as $key=>$value){
			if(!empty($value['g_image'])) {
				$re[$key]['g_image']="http://".$_SERVER['HTTP_HOST'].$value['g_image'];
			}
		}*/
		return $re;
	}

	public function add($table,$data){
		return DI()->notorm->$table->insert($data);
	}


    protected function getTableName($id){
        return 'group_base';
    }

	public function getAllGroupJoinednum($user_id)
	{
		$group_detail = DI()->notorm->group_detail;
		$rows = $group_detail->where('user_base_id=?', $user_id)->where('authorization=?', '03')->fetchRows();
		if (!empty($rows)) {
			foreach ($rows as $key => $value) {
				$row[] = $value["group_base_id"];
			}
			$groupnum = count($row);
			return $groupnum;
		}
	}

	public function getJoined($limit_st, $page_num,$user_id){
		$group_detail=DI()->notorm->group_detail;
		$rows = $group_detail->where('user_base_id=?',$user_id)->where('authorization=?','03')->fetchRows();
		if (!empty($rows)) {
		foreach ($rows as $key=>$value){
			$row[]=$value["group_base_id"];
		}
		$groupnum=count($row);
		$arr_string = join(',', $row);
		$sql="SELECT gb.name,gb.id,gb.g_image,gb.g_introduction FROM group_base gb "
			."WHERE gb.id IN($arr_string)"
			.'GROUP BY gb.id HAVING COUNT(gb.id)>=1 '
			.'ORDER BY COUNT(gb.id) DESC '
			.'LIMIT :limit_st,:page_num';
		$params = array(':limit_st' => $limit_st, ':page_num' => $page_num);
		$re=$this->getORM()->queryAll($sql, $params);
		foreach ($re as $key=>$value){
			if(!empty($value['g_image'])) {
				$re[$key]['g_image']=$value['g_image'];
			}
		}
	    }else{
			$re=array();
		}

		return $re;
	}

	public function getAllGroupCreatenum($user_id)
	{
		$group_detail = DI()->notorm->group_detail;
		$rows = $group_detail->where('user_base_id=?', $user_id)->where('authorization=?', '01')->fetchRows();
		if (!empty($rows)) {
			foreach ($rows as $key => $value) {
				$row[] = $value["group_base_id"];
			}
			$groupnum = count($row);
			return $groupnum;
		}
	}

	public function getCreate($limit_st, $page_num,$user_id){
		$group_detail=DI()->notorm->group_detail;
		$rows = $group_detail->where('user_base_id=?',$user_id)->where('authorization=?','01')->fetchRows();
		if (!empty($rows)) {
		foreach ($rows as $key=>$value){
			$row[]=$value["group_base_id"];
		}
		$groupnum=count($row);
		$arr_string = join(',', $row);
		$sql="SELECT gb.name,gb.id,gb.g_image,gb.g_introduction FROM group_base gb "
			."WHERE gb.id IN($arr_string)"
			.'GROUP BY gb.id HAVING COUNT(gb.id)>=1 '
			.'ORDER BY COUNT(gb.id) DESC '
			.'LIMIT :limit_st,:page_num';
		$params = array(':limit_st' => $limit_st, ':page_num' => $page_num);

		$re=$this->getORM()->queryAll($sql, $params);
		foreach ($re as $key=>$value){
			if(!empty($value['g_image'])) {
				$re[$key]['g_image']=$value['g_image'];
			}
		}
		}else{
			$re=array();
		}

		return $re;
	}

	public function judgeGroupExist($group_id){
		$sql=DI()->notorm->group_detail->select('group_base_id')->where('group_base_id= ?',$group_id)->fetch();
		if(!empty($sql)){
			$rs=1;
		}else{
			$rs=0;
		}
		return $rs;
	}

	public function getGroupInfo($group_id){
		$re=DI()->notorm->group_base->select('id as groupID','name as groupName','g_introduction','g_image')->where('id=?',$group_id)->fetch();
		return $re;
	}

	public function alterGroupInfo($group_id,$g_introduction,$g_image){
		$data=array('g_introduction'=>$g_introduction,
			'g_image'=>$g_image);
		$sql=DI()->notorm->group_base->where('id=?',$group_id)->update($data);
		if(isset($sql)){
			$re=1;
		}else{
			$re=0;
		}
		return $re;
	}

	public function judgeGroupUser($group_id,$user_id){
		$sql=DI()->notorm->group_detail->where('group_base_id=?',$group_id)->where('user_base_id=?',$user_id)->fetch();
		return $sql;
	}

	public function getCreator($group_id){
		$sql=DI()->notorm->group_detail->select('user_base_id')->where('group_base_id=?',$group_id)->where('authorization=?',01)->fetch();
		$sqla=DI()->notorm->user_base->select('nickname')->where('id=?',$sql['user_base_id'])->fetch();
		$sqlb=$sqla['nickname'];
		return $sqlb;
	}

	public function judgeGroupPrivate($group_id){
		$sql=DI()->notorm->group_base->select('private')->where('id=?',$group_id)->fetch();
		return $sql;
	}






}




 ?>