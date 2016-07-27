<?php
/**
* 星球接口类
*/
class Api_Group extends PhalApi_Api
{
    public function getRules(){
        return array(
            'create' => array(
                'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id',
                ),
                'g_name'    => array(
                    'name'    => 'name',
                    'type'    => 'string',
                    'require' => true,
                    'min'     => '0',
                    'max'     => '80',
                    'desc'    => '星球名称',
                ),
                'g_image' => array(
                    'name' => 'g_image',
                    'type' => 'string',
                    'require' => true,
                    'desc'=>'星球图标',
                ),
                'g_introduction'    => array(
                    'name'    => 'g_introduction',
                    'type'    => 'string',
                    'require' => false,
                    'min'     => '0',
                    'max'     => '200',
                    'desc'    => '星球简介',
                ),
            ),

            'join' => array(
                'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id',
                ),
                'g_id' => array(
                    'name' => 'group_base_id',
                    'type' => 'int',
                    'require' => true,
                    'min' => '1',
                    'desc' => '星球ID',
                ),
            ),

            'gStatus' => array(
                'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id',
                ),
                'g_id' => array(
                    'name'    => 'group_base_id',
                    'type'    => 'int',
                    'require' => true,
                    'min'     => '1',
                    'desc'    => '星球ID',
                ),
            ),

            'uStatus' => array(
                'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id',
                ),
            ),

            'lists' => array(
                'page' => array(
                    'name' => 'page',
                    'type' => 'int',
                    'require' => false,
                    'default' => 1,
                    'desc' => '当前页面',
                ),
            ),

            'posts' => array(
                'user_id'    => array(
                    'name'    => 'user_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '用户id',
                ),
                'g_id' => array(
                    'name' => 'group_base_id',
                    'type' => 'int',
                    'min' => '1',
                    'require' => true,
                    'desc' => '发帖星球',
                ),
                'title' => array(
                    'name' => 'title',
                    'type' => 'string',
                    'min' => '1',
                    'require' => true,
                    'desc' => '帖子标题',
                ),
                'text' => array(
                    'name' => 'text',
                    'type' => 'string',
                    'min' => '1',
                    'require' => true,
                    'desc' => '帖子正文',
                ),
                'p_image' => array(
                    'name' => 'p_image',
                    'type' => 'array',
                    'require' => false,
                    'desc'=>'帖子图片',
                ),
            ),

            'getjoined' => array(
                'page' => array(
                    'name' => 'page',
                    'type' => 'int',
                    'require' => false,
                    'default' => 1,
                    'desc' => '当前页面',
                ),
                'user_id' => array(
                    'name' => 'user_id',
                    'type' => 'int',
                    'require' => true,
                    'desc' => '用户id',
                ),
            ),

            'getcreate' => array(
                'page' => array(
                    'name' => 'page',
                    'type' => 'int',
                    'require' => false,
                    'default' => 1,
                    'desc' => '当前页面',
                ),
                'user_id' => array(
                    'name' => 'user_id',
                    'type' => 'int',
                    'require' => true,
                    'desc' => '用户id',
                ),
            ),

            'getgroup' =>array(
                'group_id' =>array(
                    'name' =>'group_id',
                    'type' =>'int',
                    'require' =>true,
                    'desc'=>'星球id',
                ),
                'user_id'=>array(
                    'name'=>'user_id',
                    'type'=>'int',
                    'require'=>true,
                    'desc'=>'用户id',
                    ),
            ),

            'modifygroup'=>array(
                'group_id'=>array(
                    'name'=>'group_id',
                    'type'=>'int',
                    'require'=>true,
                    'desc'=>'星球id'
                    ),
                'user_id'=>array(
                    'name'=>'user_id',
                    'type'=>'int',
                    'require'=>true,
                    'desc'=>'用户id'
                    ),
                'g_introduction'    => array(
                    'name'    => 'g_introduction',
                    'type'    => 'string',
                    'require' => true,
                    'min'     => '0',
                    'max'     => '200',
                    'desc'    => '星球简介',
                ),
            ),
        );
    }

    /**
     * 星球创建接口
     * @desc 用于创建星球
     * @return int code 操作码，1表示创建成功，0表示创建失败
     * @return object info 星球信息对象
     * @return int info.group_base_id 星球ID
     * @return string info.user_base_id 创建者ID
     * @return string info.authorization 权限，01表示创建者
     * @return string info.name 星球名称
     * @return string msg 提示信息
     */
    public function create(){
        $rs = array();

        $data = array(
            'user_id' => $this->user_id,
            'name'    => $this->g_name,
            'g_image'    => $this->g_image,
            'g_introduction' => $this->g_introduction,
        );
        $domain = new Domain_Group();
        $rs = $domain->create($data);
        return $rs;
    }
    /**
     * 加入星球接口
     * @desc 用户加入星球
     * @return int code 操作码，1表示加入成功，0表示加入失败
     * @return object info 星球信息对象
     * @return int info.group_base_id 加入星球ID
     * @return string info.user_base_id 加入者ID
     * @return string info.authorization 权限，03表示会员
     * @return string msg 提示信息
     */
    public function join(){
        $rs = array();
        $data = array(
            'user_id' => $this->user_id,
            'g_id'    => $this->g_id,
        );
        $domain = new Domain_Group();
        $rs = $domain->join($data);

        return $rs;
    }

    /**
     * 判断用户登陆状态
     * @desc 判断是否登录
     * @return int code 操作码，1表示已登录，0表示未登录
     * @return object info 状态信息对象
     * @return int info.id 用户ID
     * @return string info.nickname 用户昵称
     * @return string msg 提示信息
     */
    public function uStatus(){
        $rs = array();
        $data = array(
            'user_id' => $this->user_id,
        );
        $domain = new Domain_Group();
        $rs = $domain->uStatus($data);

        return $rs;
    }

    /**
     * 判断用户是否加入该星球
     * @desc 判断用户是否加入该星球
     * @return int code 操作码，1表示已加入，0表示未加入
     * @return string msg 提示信息
     */
    public function gStatus(){
        $rs = array();
        $data = array(
            'user_id' => $this->user_id,
            'g_id'    => $this->g_id,
        );
        $domain = new Domain_Group();
        $rs = $domain->gStatus($data);

        return $rs;
    }

    // /**
    //  * 登出接口
    //  * @desc 注销
    //  * @return_ int code 操作码，1表示注销成功，0表示注销失败
    //  */
    // public function loginOut(){

    //  $domain = new Domain_Group();
    //  $domain->out();
    // }

    /**
     * 星球列表
     * @desc 按成员数降序显示星球列表
     * @return int lists 星球列表对象
     * @return string lists.name 星球名称
     * @return string lists.g_image 星球图片
     * @return string lists.g_introduction 星球介绍
     * @return int lists.num 星球成员数
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function lists(){
        $rs = array(
            'lists'  => array(),
            );
        $pages = 20;                //每页数量
        $domain = new Domain_Group();
        $rs['lists'] =  $domain->lists($this->page, $pages);
        $rs['pageCount'] = $domain->pages['pageCount'];
        $rs['currentPage'] = $domain->pages['currentPage'];
        return $rs;
    }

    /**
     * 帖子发布
     * @desc 星球帖子发布
     * @return int code 操作码，1表示发布成功，0表示发布失败
     * @return object info 帖子信息对象
     * @return int info.group_base_id 帖子所属星球ID
     * @return int info.post_base_id 帖子ID
     * @return string info.text 帖子正文
     * @return int info.floor 帖子楼层
     * @return string info.createTime 帖子发布时间
     * @return string info.title 帖子标题
     * @return string msg 提示信息
     */
    public function posts(){
        $rs = array();
        $data = array(
                'user_id'       => $this->user_id,
                'group_base_id' => $this->g_id,
                'title'         => $this->title,
                'text'          => $this->text,
                'p_image'       => $this->p_image,
            );

        $domain = new Domain_Group();
        $rs = $domain->posts($data);

        return $rs;
    }

    /**
     * 通过用户id找出已加入的星球
     * @desc 按成员数降序显示星球列表
     * @return int code 操作码，1表示加入成功，0表示加入失败
     * @return object groups 星球列表对象
     * @return int groups.name 星球名称
     * @return int groups.id 星球ID
     * @return string groups.g_image 星球图片
     * @return int num 星球成员数
     * @return string groups.g_introduction 星球介绍
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function getJoined()
    {
        $user_id=$this->user_id;
        $rs = array(
            'groups' => array(),
        );
        $pages = 20;                //每页数量
        $domain = new Domain_Group();
        $rs['groups'] = $domain->getJoined($this->page, $pages, $user_id);
        $rs['pageCount'] = $domain->pages['pageCount'];
        $rs['currentPage'] = $domain->pages['currentPage'];
        $rs['num']=$domain->pages['num'];
        $rs['user_name']=$domain->pages['user_name'];
        return $rs;
    }

    /**
     * 通过用户id找出已创建的星球
     * @desc 按成员数降序显示星球列表
     * @return int code 操作码，1表示加入成功，0表示加入失败
     * @return object groups 星球列表对象
     * @return int groups.name 星球名称
     * @return int groups.id 星球ID
     * @return string groups.g_image 星球图片
     * @return int num 星球成员数
     * @return string groups.g_introduction 星球介绍
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function getCreate()
    {
        $user_id=$this->user_id;
        $rs = array(
            'groups' => array(),
        );
        $pages = 20;                //每页数量
        $domain = new Domain_Group();
        $rs['groups'] = $domain->getCreate($this->page, $pages, $user_id);
        $rs['pageCount'] = $domain->pages['pageCount'];
        $rs['currentPage'] = $domain->pages['currentPage'];
        $rs['num']=$domain->pages['num'];
        $rs['user_name']=$domain->pages['user_name'];
        return $rs;
    }
/**
 *获取星球详情
 * @return int  ret   操作码 200代表成功
 * @return object data 星球信息对象
 * @return int data.id 星球id
 * @return string data.name 星球名称
 * @return string data.g_introduction 星球介绍
 * @return int data.cteate 判断是否为创建者 1为创建者
 * @return string msg 报错信息
 */
    public function getGroup()
    {
        $group_id=$this->group_id;
        $user_id=$this->user_id;
        $common = new Domain_Common();
        $user = new Domain_User();
        $exist = $common->judgeGroupExist($group_id);
        $create = $user->judgeCreate($user_id,$group_id);
        if($exist){
                $group = new Domain_Group();
                $rs=$group->getGroup($group_id);
            if($create){
               $rs['create']=1;
        }else{
               $rs['create']=0;
        }
        }else{
            $rs=0;
        }
        return $rs;
    }
/**
 *修改星球详情
 * @return int  ret   操作码 200代表成功
 * @return int data 2代表不是创建者 3代表星球不存在 1代表修改成功 0代表没有改动
 * @return string msg 信息
 */
    public function modifyGroup(){
        $group_id=$this->group_id;
        $user_id=$this->user_id;
        $g_introduction=$this->g_introduction;
        $common = new Domain_Common();
        $user = new Domain_User();
        $exist = $common->judgeGroupExist($group_id);
        $create = $user->judgeCreate($user_id,$group_id);
        if($exist){
            if($create){
                $group = new Domain_Group();
                $rs=$group->modifyGroup($group_id,$g_introduction);
        }else{
            $rs=2;
        }
        }else{
            $rs=3;
        }
        return $rs;
    }






}





 ?>