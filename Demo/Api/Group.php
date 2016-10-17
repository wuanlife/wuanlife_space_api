<?php
/**
* 星球接口类
*/
class Api_Group extends PhalApi_Api
{
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
                'text' => array(
                    'name'    => 'text',
                    'type'    => 'string',
                    'max'     => '200',
                    'require' => false,
                    'desc'    => '申请信息'
                ),
            ),

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
                    'name'      => 'g_image',
                    'type'      => 'string',
                    'require'   => true,
                    'desc'      =>'星球图标',
                ),
                'g_introduction'    => array(
                    'name'    => 'g_introduction',
                    'type'    => 'string',
                    'require' => false,
                    'min'     => '0',
                    'max'     => '200',
                    'desc'    => '星球简介',
                ),
                'private'=>array(
                    'name'    =>'private',
                    'type'    =>'int',
                    'require' =>false,
                    'desc'    =>'私密/私密为1',
                    'default' =>0,
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
                    'name'      => 'group_base_id',
                    'type'      => 'int',
                    'require'   => true,
                    'min'       => '1',
                    'desc'      => '星球ID',
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
                    'name'          => 'group_base_id',
                    'type'          => 'int',
                    'min'           => '1',
                    'require'       => true,
                    'desc'          => '发帖星球',
                ),
                'title' => array(
                    'name'      => 'title',
                    'type'      => 'string',
                    'min'       => '1',
                    'max'       =>'150',
                    'require'   => true,
                    'desc'      => '帖子标题',
                ),
                'text' => array(
                    'name'      => 'text',
                    'type'      => 'string',
                    'min'       => '1',
                    'require'   => true,
                    'desc'      => '帖子正文',
                ),
                'p_image' => array(
                    'name'      => 'p_image',
                    'type'      => 'array',
                    'require'   => false,
                    'desc'      =>'帖子图片',
                ),
            ),

            'getjoined' => array(
                'page' => array(
                    'name'      => 'page',
                    'type'      => 'int',
                    'require'   => false,
                    'default'   => 1,
                    'desc'      => '当前页面',
                ),
                'user_id' => array(
                    'name'      => 'user_id',
                    'type'      => 'int',
                    'require'   => true,
                    'desc'      => '用户id',
                ),
            ),

            'getcreate' => array(
                'page' => array(
                    'name'      => 'page',
                    'type'      => 'int',
                    'require'   => false,
                    'default'   => 1,
                    'desc'      => '当前页面',
                ),
                'user_id' => array(
                    'name'      => 'user_id',
                    'type'      => 'int',
                    'require'   => true,
                    'desc'      => '用户id',
                ),
            ),

            'getGroupInfo' =>array(
                'group_id' =>array(
                    'name'      =>'group_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'星球id',
                ),
                'user_id'=>array(
                    'name'      =>'user_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'用户id',
                    ),
            ),
            'UserManage' =>array(
                'group_id' =>array(
                    'name'      =>'group_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'星球id',
                ),
                'user_id'=>array(
                    'name'      =>'user_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'用户id',
                    ),
            ),
            'deleteGroupMember' =>array(
                'group_id' =>array(
                    'name'      =>'group_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'星球id',
                ),
                'user_id'=>array(
                    'name'      =>'user_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'用户id',
                    ),
                'member_id'=>array(
                    'name'      =>'member_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'星球成员id',
                    ),
            ),

            'alterGroupInfo'=>array(
                'group_id'=>array(
                    'name'      =>'group_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'星球id'
                    ),
                'user_id'=>array(
                    'name'      =>'user_id',
                    'type'      =>'int',
                    'require'   =>true,
                    'desc'      =>'用户id'
                    ),
                'g_introduction'    => array(
                    'name'    => 'g_introduction',
                    'type'    => 'string',
                    'require' => false,
                    'min'     => '0',
                    'max'     => '200',
                    'desc'    => '星球简介',
                ),
                'g_image'=>array(
                    'name'      =>'g_image',
                    'type'      =>'string',
                    'require'   =>false,
                    'desc'      =>'星球图片',
                ),
            ),
            'search' =>array(
                'text' =>array(
                    'name'      =>'text',
                    'type'      =>'string',
                    'require'   =>true,
                    'desc'      =>'搜索内容',
                ),
                'gn' =>array(
                    'name'      =>'gn',
                    'type'      =>'int',
                    'require'   =>false,
                    'desc'      =>'星球页数',
                ),
                'pn' =>array(
                    'name'      =>'pn',
                    'type'      =>'int',
                    'require'   =>false,
                    'desc'      =>'帖子页数',
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
        $common=new Domain_Common();
        $private=$common->judgePrivate($this->private);
        $data = array(
            'user_id'        => $this->user_id,
            'name'           => $this->g_name,
            'g_image'        => $this->g_image,
            'g_introduction' => $this->g_introduction,
            'private'        => $private,
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
* @desc 获取星球详情接口
 * @return int groupID 星球id
 * @return string groupName 星球名称
 * @return string g_introduction 星球介绍
 * @return string g_image 星球图片链接
 * @return int creator 是否为创建者，1为创建者，0不是创建者
 */
    public function getGroupInfo()
    {
        $group_id=$this->group_id;
        $user_id=$this->user_id;
        $common = new Domain_Common();
        $user = new Domain_User();
        $exist = $common->judgeGroupExist($group_id);
        $createor = $user->judgeCreate($user_id,$group_id);
        if($exist){
                $group = new Domain_Group();
                $rs=$group->getGroupInfo($group_id);
            if($createor){
               $rs['creator']=1;
        }else{
               $rs['creator']=0;
        }
        }else{
            $rs=0;
        }
        return $rs;
    }
/**
 *修改星球接口
* @desc 修改星球详情
 * @return int data 0代表修改失败,1代表修改成功
 * @return string msg 提示错误信息
 */
    public function alterGroupInfo(){
        $group_id=$this->group_id;
        $user_id=$this->user_id;
        $g_introduction=$this->g_introduction;
        $g_image=$this->g_image;
        $common = new Domain_Common();
        $user = new Domain_User();
        $exist = $common->judgeGroupExist($group_id);
        $create = $user->judgeCreate($user_id,$group_id);
        if($exist){
            if($create){
                $group = new Domain_Group();
                $rs=$group->alterGroupInfo($group_id,$g_introduction,$g_image);
        }else{
            $rs=2;
        }
        }else{
            $rs=3;
        }
        if($rs==1){
            $re['data']=1;
            $re['msg']='修改成功';
        }else if($rs==2){
            $re['data']=0;
            $re['msg']='不是创建者';
        }else if($rs==3){
            $re['data']=0;
            $re['msg']='星球不存在';
        }
        return $re;
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
            'text' => $this->text,
            );
        $domain = new Domain_Group();
        $rs = $domain->PrivateGroup($data);
        $common=new Domain_Common();
        $userID=$common->getGroupCreate($data['group_id']);
        $re=$common->judgeUserOnline($userID);
        if(empty($re)){
            $rs['code']=2;
        }

        return $rs;
    }
/**
 * 星球用户管理接口
 * @desc 用于显示加入星球的用户，方便管理
 * @return int code 操作码，1表示成功，0表示失败
 * @return string msg 提示信息
 * @return array info 用户信息详情
 * @return string info.user_id 用户id
 * @return string info.user_name 用户昵称
 */
    public function UserManage(){
        $data = array(
            'user_id'    => $this->user_id,
            'group_id'   => $this->group_id,
            );
        $domain_c = new Domain_Common();
        $create = $domain_c->judgeGroupCreator($data['group_id'],$data['user_id']);
        if($create){
            $domain = new Domain_Group();
            $rs = $domain->UserManage($data);
        }else{
            $rs =array(
                    'code' =>0,
                    'msg'  =>'您不是星球创建者，没有权限！',
            );
        }
        return $rs;
    }
/**
 * 星球用户删除接口
 * @desc 用于删除加入星球的用户
 * @return int code 操作码，1表示成功，0表示失败
 * @return string msg 提示信息
 */
    public function deleteGroupMember(){
        $data = array(
            'user_id'    => $this->user_id,
            'group_id'   => $this->group_id,
            'member_id'  => $this->member_id,
            );
        $domain_c = new Domain_Common();
        $create = $domain_c->judgeGroupCreator($data['group_id'],$data['user_id']);
        if($create){
            $domain = new Domain_Group();
            $rs = $domain->deleteGroupMember($data);
        }else{
            $rs =array(
                    'code' =>0,
                    'msg'  =>'您不是星球创建者，没有权限！',
            );
        }
        return $rs;
    }

/**
 *搜索接口
* @desc 搜索接口
 * @return string group.name 星球名称
 * @return int group.id 星球ID
 * @return string group.g_image 星球图片链接
 * @return string group.g_introduction 星球介绍
 * @return int group.num 星球成员数
 * @return int posts.postID 帖子ID
 * @return string posts.title 标题
 * @return string posts.text 内容
 * @return date posts.createTime 发帖时间
 * @return string posts.nickname 发帖人
 * @return int posts.groupID 星球ID
 * @return string posts.groupName 星球名称
 */
    public function search(){
        $domainGroup=new Domain_Group();
        $domainPosts=new Domain_Post();
        $group=$domainGroup->searchGroup($this->text,$this->gn);
        $posts=$domainPosts->searchPosts($this->text,$this->pn);
        $data=array_merge($group,$posts);
        return $data;
    }
}
 ?>