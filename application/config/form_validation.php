<?php
$config = array(
    'error_prefix' =>'',
    'error_suffix' =>'',
    'user_info'     =>array(
        array(
            'field' => 'name',
            'label' => '用户昵称',
            'rules' => 'regex_match[/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u]'
        ),
        array(
            'field' => 'avatar_url',
            'label' => '用户头像',
            'rules' => 'regex_match[/([http|https]):\/\/.*?\.([gif|jpg|png])/]'
        ),
        array(
            'field' => 'sex',
            'label' => '性别',
            'rules' => 'in_list[man,woman,secret]'
        ),
//        array(
//            'field' => 'birthday',
//            'label' => '生日',
//            'rules' => ''
//        ),
    ),
    'comment_delete' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'floor',
            'label' => '楼层数',
            'rules' => 'required|greater_than_equal_to[2]|is_natural_no_zero'
        ),
    ),
    'post_comment'=>array(
        array(
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ),
        array(
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        ),
        array(
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required'
        ),
        array(
            'field' => 'reply_floor',
            'label' => '楼层数',
            'rules' => 'is_natural_no_zero'
        )
    ),
    'lists'      =>array(
        array(
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ),
        array(
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        )
    ),
    'join_group' =>array(
        array(
            'field' => 'group_id',
            'label' => '星球名称',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        )
    ),
    'create_group' =>array(
        array(
            'field' => 'g_name',
            'label' => '星球名称',
            'rules' => 'required|min_length[1]|max_length[80]|regex_match[/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u]|is_string'
        ),
        array(
            'field' => 'g_introduction',
            'label' => '星球简介',
            'rules' => 'min_length[1]|max_length[50]'
        ),
        array(
            'field' => 'g_image',
            'label' => '星球图片',
            'rules' => 'valid_url'
        )
    ),
    'message' => array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|is_natural_no_zero'
        ),
        array(
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ),
        array(
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        ),
        array(
            'field' => 'type',
            'label' => '消息分类',
            'rules' => 'in_list[home,apply,group,post]'
        )
    ),
    'check_token'=>array(
        array(
            'field' => 'token',
            'label' => '身份信息',
            'rules' => 'required'
        ),
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|is_natural_no_zero'
        )
    ),
    'send_mail'=>array(
        array(
            'field' => 'email',
            'label' => '邮箱',
            'rules' => 'required|valid_email'
        )
    ),
    'login'=>array(
        array(
            'field' => 'email',
            'label' => '邮箱',
            'rules' => 'required|valid_email'
        ),
        array(
            'field' => 'password',
            'label' => '密码',
            'rules' => 'required|min_length[6]'
        )
    ),
    're_psw'=>array(
        array(
            'field' => 'password',
            'label' => '密码',
            'rules' => 'required|min_length[6]'
        ),
        array(
            'field' => 'token',
            'label' => '身份信息',
            'rules' => 'required'
        )
    ),
    'reg'=>array(
        array(
            'field' => 'nickname',
            'label' => '昵称',
            'rules' => 'required'
        ),
        array(
            'field' => 'email',
            'label' => '邮箱',
            'rules' => 'required|valid_email'
        ),
        array(
            'field' => 'password',
            'label' => '密码',
            'rules' => 'required|min_length[6]'
        ),
        array(
            'field' => 'code',
            'label' => '邀请码',
            'rules' => 'required'
        ),
    ),
    'show_message' => array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required'
        ),
        array(
            'field' => 'pn',
            'label' => 'pn',
            'rules' => 'required'
        ),
        array(
            'field' => 'm_type',
            'label' => 'm_type',
            'rules' => 'required'
        )
    ),
    'get_post_base' => array(
        array(
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required'
        )
    ),
    'process_apply' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required'
        ),
        array(
            'field' => 'm_id',
            'label' => '消息ID',
            'rules' => 'required'
        ),
//        array(
//            'field' => 'mark',
//            'label' => '操作码',
//            'rules' => 'in_list[true,false]'
//        )
    ),
    'private_group' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'group_id',
            'label' => 'group_id',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'text',
            'label' => 'text',
            'rules' => 'min_length[1]|max_length[50]'
        )
    ),
    'posts' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'group_id',
            'label' => '星球ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'content',
            'label' => '帖子内容',
            'rules' => 'required|min_length[1]|max_length[49999]'
        ),
        array(
            'field' => 'title',
            'label' => '帖子标题',
            'rules' => 'required|min_length[1]|max_length[60]'
        )
    ),
    'change_pwd' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'password',
            'label' => '原密码',
            'rules' => 'required|min_length[6]'
        ),
        array(
            'field' => 'psw',
            'label' => '新密码',
            'rules' => 'required|min_length[6]'
        )
    ),
    'edit_post' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'content',
            'label' => '帖子内容',
            'rules' => 'required|min_length[1]|max_length[49999]'
        ),
        array(
            'field' => 'title',
            'label' => '标题',
            'rules' => 'required|min_length[1]|max_length[60]'
        )
    ),
    'post_reply' =>array(
        array(
            'field' => 'user_base_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'post_base_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'comment',
            'label' => '回复内容',
            'rules' => 'required|min_length[1]|max_length[5000]'
        )
    ),
    'collect_post' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
        array(
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ),
    ),
    'delete_group_member' =>array(
        array(
            'field' => 'group_id',
            'label' => '星球ID',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'member_id',
            'label' => '成员ID',
            'rules' => 'required|min_length[1]'
        )
    ),
    'get_create' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required'
        ),
    ),
    'get_group_post' =>array(
        array(
            'field' => 'group_id',
            'label' => '星球ID',
            'rules' => 'required'
        ),
        array(
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ),
        array(
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        )
    ),
    'search' =>array(
        array(
            'field' => 'text',
            'label' => 'text',
            'rules' => 'required'
        ),
    ),
    'delete_message' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|is_natural_no_zero'
        ),
        array(
            'field' => 'id',
            'label' => '消息ID',
            'rules' => 'required|is_natural_no_zero'
        ),
    ),
    'email' => array(
        array(
            'field' => 'emailaddress',
            'label' => 'EmailAddress',
            'rules' => 'required|valid_email'
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|alpha'
        ),
        array(
            'field' => 'title',
            'label' => 'Title',
            'rules' => 'required'
        ),
        array(
            'field' => 'message',
            'label' => 'MessageBody',
            'rules' => 'required'
        )
    )
);