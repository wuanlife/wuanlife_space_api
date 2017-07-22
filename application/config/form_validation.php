<?php
$config = array(
    'error_prefix' =>'',
    'error_suffix' =>'',
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
            'rules' => 'required'
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
            'label' => 'post_id',
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
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'group_id',
            'label' => 'group_id',
            'rules' => 'required|min_length[1]'
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
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'group_id',
            'label' => 'group_id',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'p_text',
            'label' => 'p_text',
            'rules' => 'required|min_length[1]|max_length[5000]'
        ),
        array(
            'field' => 'p_title',
            'label' => 'p_title',
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
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'post_id',
            'label' => 'post_id',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'text',
            'label' => 'p_text',
            'rules' => 'required|min_length[1]|max_length[5000]'
        ),
        array(
            'field' => 'title',
            'label' => 'p_title',
            'rules' => 'required|min_length[1]|max_length[60]'
        )
    ),
    'post_reply' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'post_id',
            'label' => 'post_id',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'p_text',
            'label' => 'p_text',
            'rules' => 'required|min_length[1]|max_length[5000]'
        )
    ),
    'collect_post' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'post_id',
            'label' => 'post_id',
            'rules' => 'required|min_length[1]'
        ),
    ),
    'delete_group_member' =>array(
        array(
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'group_id',
            'label' => 'group_id',
            'rules' => 'required|min_length[1]'
        ),
        array(
            'field' => 'member_id',
            'label' => 'member_id',
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
            'label' => 'group_id',
            'rules' => 'required'
        ),
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