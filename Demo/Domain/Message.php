<?php

class Domain_Message {
	
/*
 * 用于申请加入私密星球
 */
	public function PrivateGroup($data) {
		$model = new Model_Message();
        $rs = $model->PrivateGroup($data);
		if($rs) {
			$this->code = 1;
			$this->msg = '申请成功！请等待创建者审核！';
		}else {
			$this->code = 0;
			$this->msg = '申请失败！';
		}
		return $this;
	}
	
/*
 * 用于同意加入私密星球的申请
 */
	public function AgreeApp($data){
		$model = new Model_Message();
        $rs = $model->AgreeApp($data);
		$this->code = 0;
		if($rs == 1) {
			$this->code = 1;
			$this->msg = '操作成功！您已同意该成员的申请！';
		}elseif($rs == 0) {
			$this->msg = '操作失败！';
		}elseif($rs == 2){
			$this->msg = '您不是创建者，没有权限！';
		}
		return $this;
	}
	
/*
 * 用于拒绝加入私密星球的申请
 */
	public function DisagreeApp($data){
		$model = new Model_Message();
        $rs = $model->DisagreeApp($data);
		$this->code = 0;
		if($rs == 1) {
			$this->code = 1;
			$this->msg = '操作成功！您已拒绝该成员的申请！';
		}elseif($rs == 0) {
			$this->msg = '操作失败！';
		}elseif($rs == 2){
			$this->msg = '您不是创建者，没有权限！';
		}
		return $this;
	}

/*
 * 用于显示用户的消息列表
 */
	public function ShowMessage($data){
		$model = new Model_Message();
        $rs = $model->ShowMessage($data);
		//$this->code = 0;
		return $rs;
	}
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
}