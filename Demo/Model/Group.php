<?php
/**
* 星球相关DB操作
*/
class Model_Group extends PhalApi_Model_NotORM{
    protected function getTableName($id){
        return 'group_base';
    }

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
            .'where gb.id = gd.group_base_id AND gb.delete=0 '
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            .'LIMIT :limit_st,:page_num';
        $params = array(':limit_st' => $limit_st, ':page_num' => $page_num);
        $re = $this->getORM()->queryAll($sql, $params);
/*      foreach ($re as $key=>$value){
            if(!empty($value['g_image'])) {
                $re[$key]['g_image']="http://".$_SERVER['HTTP_HOST'].$value['g_image'];
            }
        }*/
        return $re;
    }

    public function add($table,$data){
        return DI()->notorm->$table->insert($data);
    }

    public function getAllGroupJoinednum($user_id)
    {
        $group_detail = DI()->notorm->group_detail;
        $rows = $group_detail->where('user_base_id=?', $user_id)->where('authorization=?', '03')->fetchRows();
        return $rows;
    }

    public function getJoined($limit_st, $page_num,$user_id){
        $rows = $this->getAllGroupJoinednum($user_id);
        if (!empty($rows)) {
        foreach ($rows as $key=>$value){
            $row[]=$value["group_base_id"];
        }
        $arr_string = join(',', $row);
        $sql="SELECT gb.name,gb.id,gb.g_image,gb.g_introduction,COUNT(gd.group_base_id) AS num FROM group_base gb,group_detail gd "
            ."WHERE gb.delete=0 AND gb.id IN($arr_string) AND gb.id=gd.group_base_id "
            .'GROUP BY gb.id HAVING COUNT(gb.id)>=1 '
            .'ORDER BY COUNT(gd.group_base_id) DESC '
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
        return $rows;
        }

/*
 * 加入星球
 */
    public function join($data){
        $result = DI()->notorm->group_detail->insert($data);
    }
/*
 * 告知星球创建者已加入星球
 */
    public function joinMessage($data){
        $data['group_id'] = $data['group_base_id'];
        $creator = $this->getCreatorId($data);
        $field=array(
                    'message_base_code'=>'0006',
                    'user_base_id'=>$creator,
                    'id_1'    =>$data['user_base_id'],
                    'id_2'    =>$data['group_id'],
                    'createTime'=>time(),
        );
        $sql = DI()->notorm->message_detail->insert($field);
        if($sql){
            return true;
        }else{
            return false;
        }
    }
/*
 * 退出星球
 */
    public function quit($data){
        $result = DI()->notorm->group_detail->where('group_base_id=?',$data['group_base_id'])->where('user_base_id=?',$data['user_base_id'])->where('authorization=?',03)->delete();
    }
/*
 * 告知星球创建者已退出星球
 */
    public function quitMessage($data){
        $data['group_id'] = $data['group_base_id'];
        $creator = $this->getCreatorId($data);
        $field=array(
                    'message_base_code'=>'0005',
                    'user_base_id'=>$creator,
                    'id_1'    =>$data['user_base_id'],
                    'id_2'    =>$data['group_id'],
                    'createTime'=>time(),
        );
        $sql = DI()->notorm->message_detail->insert($field);
        if($sql){
            return true;
        }else{
            return false;
        }
    }

    public function judgeGroupCreator($group_id,$user_id){
        $sql=DI()->notorm->group_detail->where('group_base_id=?',$group_id)->where('user_base_id=?',$user_id)->where('authorization=?',01)->fetch();
        if(empty($sql)){
            $re=NULL;
        }else{
            $re=1;
        }
        return $re;
    }

    public function judgeGroupUser($group_id,$user_id){
        $sql=DI()->notorm->group_detail->where('group_base_id=?',$group_id)->where('user_base_id=?',$user_id)->where('authorization=?',03)->fetch();
        if(empty($sql)){
            $re=NULL;
        }else{
            $re=1;
        }
        return $re;
    }

    public function getCreator($group_id){
        $sql=DI()->notorm->group_detail->select('user_base_id')->where('group_base_id=?',$group_id)->where('authorization=?',01)->fetch();
        $sqla=DI()->notorm->user_base->select('nickname')->where('id=?',$sql['user_base_id'])->fetch();
        $sqlb=$sqla['nickname'];
        return $sqlb;
    }

    public function judgeGroupPrivate($group_id){
        $sql=DI()->notorm->group_base->select('private')->where('id=?',$group_id)->fetch();
        return $sql['private'];
    }

    public function getCreate($limit_st, $page_num,$user_id){
        $rows = $this->getAllGroupCreatenum($user_id);
        if (!empty($rows)) {
        foreach ($rows as $key=>$value){
            $row[]=$value["group_base_id"];
        }
        $arr_string = join(',', $row);
        $sql="SELECT gb.name,gb.id,gb.g_image,gb.g_introduction,COUNT(gd.group_base_id) AS num FROM group_base gb,group_detail gd "
            ."WHERE gb.delete=0 AND gb.id IN($arr_string) AND gb.id=gd.group_base_id "
            .'GROUP BY gb.id HAVING COUNT(gb.id)>=1 '
            .'ORDER BY COUNT(gd.group_base_id) DESC '
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
        $sql=DI()->notorm->group_base->select('id')->where(array('id'=>$group_id,'`delete`'=>'0'))->fetch();
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
/*
 * 插入私密星球相关信息到数据库
 */
    public function PrivateGroup($data) {
        $founder_id = $this->getCreatorId($data); //通过星球id查找创建者id
        $message_base_code = '0001';
        //$maxcount = $this->getMaxCount($message_base_code,$founder_id);
        $field = array(
                    'message_base_code' =>$message_base_code,
                    'user_base_id'      =>$founder_id,
                    /*
                    'count'             =>$maxcount+1,
                    */
                    'id_1'              =>$data['user_id'],
                    'id_2'              =>$data['group_id'],
                    'createTime'        =>time(),
        );
        $sql = DI()->notorm->message_detail->insert($field);
        if($sql){
            $rs = 1;
            $field = array(
                        'message_detail_id' =>$sql['id'],
                        'text'              =>$data['text'],
            );
            if(!empty($data['text'])){
            DI()->notorm->message_text->insert($field);
            }
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
 * 查找消息列表中count的最大值

    public function getMaxCount($message_base_code,$id) {
        $sql = DI()->notorm->message_detail
        ->select('max(count)')
        ->where('message_base_code= ?',$message_base_code)
        ->where('user_base_id= ?',$id)
        ->fetch();
        return $sql['max(count)'];
    }
    数据库结构改变，此处注释不再使用
 */
    public function getGroupName($group_id){
        $sql=DI()->notorm->group_base->select('name')->where('id=?',$group_id)->fetch();
        return $sql['name'];
    }

    public function getGroupCreate($group_id){
        $sql=DI()->notorm->group_detail->select('user_base_id')->where('group_base_id',$group_id)->where('authorization','01')->fetch();
        return $sql['user_base_id'];
    }

    public function judgeUserApplication($user_id,$group_id){
        $sql=DI()->notorm->message_detail->where('status',array(0,1))->where('message_base_code','0001')->AND('id_1',$user_id)->AND('id_2',$group_id)->fetch();
        return $sql;
    }
/*
 * 用于显示加入星球的用户，方便管理
 */
    public function UserManage($data){
        $rs=DI()->notorm->group_detail->where('group_base_id',$data['group_id'])->where('authorization','03')->fetchAll();
        return $rs;
    }
/*
 * 用于删除加入星球的用户
 */
    public function deleteGroupMember($data){
        $rs=DI()->notorm->group_detail->where('group_base_id',$data['group_id'])->where('user_base_id',$data['member_id'])->where('authorization','03')->delete();
        return $rs;
    }

/*
 * 告知被星球创建者删除的用户
 */
    public function dgmMessage($data){
        $field=array(
                    'message_base_code'=>'0004',
                    'user_base_id'=>$data['member_id'],
                    'id_1'    =>$data['user_id'],
                    'id_2'    =>$data['group_id'],
                    'createTime'=>time(),
        );
        $sql = DI()->notorm->message_detail->insert($field);
        if($sql){
            return true;
        }else{
            return false;
        }
    }
    public function searchGroup($text,$gnum,$gn){
        if(empty($gn)){
            $re['group'] = array();
            return $re;
        }
        $text = strtolower($text);
        $num=($gn-1)*$gnum;
        $sql='SELECT gb.name,gb.id,gb.g_image,gb.g_introduction,COUNT(gd.user_base_id) AS num FROM group_detail gd, group_base gb '
            .'where gb.id = gd.group_base_id AND gb.delete=0 '
            ."AND lower(gb.name) LIKE '%$text%' "
            .'GROUP BY gd.group_base_id HAVING COUNT(gd.user_base_id)>=1 '
            .'ORDER BY COUNT(gd.user_base_id) DESC '
            ."LIMIT $num,$gnum";
        $re['group'] = $this->getORM()->queryAll($sql);
        return $re;
    }

    public function searchGroupNum($text){
        $text = strtolower($text);
        $sql='SELECT COUNT(group_base.id) AS num FROM group_base '
            .'WHERE group_base.delete=0 '
            ."AND lower(group_base.name) LIKE '%$text%' ";
        $re = $this->getORM()->queryAll($sql);
        return $re[0]['num'];
    }
}





 ?>