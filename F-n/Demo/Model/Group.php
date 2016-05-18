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
		return DI()->notorm->user_base->select('nickname')->where('id = ?', $user_id)->fetchOne();
	}

	public function getAllNum(){
		return $this->getORM()->count('id');
	}

	public function lists($limit_st, $page_num){
		$sql='SELECT gb.name,gb.id,COUNT(gd.user_base_id) AS num FROM group_detail gd, group_base gb '
			.'where gb.id = gd.group_base_id '
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            .'LIMIT :limit_st,:page_num';
        $params = array(':limit_st' => $limit_st, ':page_num' => $page_num);
        return $this->getORM()->queryAll($sql, $params);
	}

	public function add($table,$data){
		return DI()->notorm->$table->insert($data);
	}


    protected function getTableName($id){
        return 'group_base';
    }
	
    public function sPost($data){
    	$rs = array();
    	
    	$sqla=DI()->notorm->post_base
    	->select('group_base_id')
    	->where('id=?',$data['post_id'])
    	->fetchone();
    	
    	$sqlb=DI()->notorm->group_detail
    	->select('user_base_id')
    	->where('group_base_id=?',$sqla['group_base_id'])
    	->fetchone();
    	
    	$s_data = array(
    			'sticky' => '1',
    	);
    	
    	if($data['user_id']==$sqlb['user_base_id']) {
    		$s = DI()->notorm->post_base
    		->where('id =?', $data['post_id'])
    		->update($s_data);
    		$rs['code']=1;
    		$rs['re']="操作成功";
    	}else{
            $rs['code']=0;
            $rs['re']="仅星球创建者能置顶帖子!";
        }   	
    	return $rs;
    }
    
    public function unSPost($data){
    	$rs = array();
    	 
    	$sqla=DI()->notorm->post_base
    	->select('group_base_id')
    	->where('id=?',$data['post_id'])
    	->fetchone();
    	 
    	$sqlb=DI()->notorm->group_detail
    	->select('user_base_id')
    	->where('group_base_id=?',$sqla['group_base_id'])
    	->fetchone();
    	 
    	$s_data = array(
    			'sticky' => '0',
    	);
    	 
    	if($data['user_id']==$sqlb['user_base_id']) {
    		$s = DI()->notorm->post_base
    		->where('id =?', $data['post_id'])
    		->update($s_data);
    		$rs['code']=1;
    		$rs['re']="操作成功";
    	}else{
    		$rs['code']=0;
    		$rs['re']="仅星球创建者能取消置顶帖子!";
    	}
    	return $rs;
    }
    
    public function dPost($data){
    	$rs = array();
    
    	$sqla=DI()->notorm->post_base
    	->select('group_base_id')
    	->where('id=?',$data['post_id'])
    	->fetchone();
    
    	$sqlb=DI()->notorm->group_detail
    	->select('user_base_id')
    	->where('group_base_id=?',$sqla['group_base_id'])
    	->fetchone();
    
    	$d_data = array(
    			'`delete`' => '1',
    	);
    
    	if($data['user_id']==$sqlb['user_base_id']) {
    		$sa = DI()->notorm->post_base
    		->where('id =?', $data['post_id'])
    		->update($d_data);
    		$sb = DI()->notorm->post_detail
    		->where('post_base_id=?', $data['post_id'])
    		->update($d_data);
    		$rs['code']=1;
    		$rs['re']="操作成功";
    	}else{
    		$rs['code']=0;
    		$rs['re']="仅星球创建者能删除帖子!";
    	}
    	return $rs;
    }
    
}






 ?>