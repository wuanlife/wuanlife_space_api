<?php
$config = [
    'error_prefix'        => '',
    'error_suffix'        => '',
    'approve_post'        => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'floor',
            'label' => '楼层数',
            'rules' => 'required|greater_than_equal_to[1]|is_natural_no_zero'
        ],
    ],
    'user_info'           => [
        [
            'field' => 'name',
            'label' => '用户昵称',
            'rules' => 'regex_match[/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u]'
        ],
        [
            'field' => 'avatar_url',
            'label' => '用户头像',
            'rules' => 'regex_match[/([http|https]]:\/\/.*?\.([gif|jpg|png]]/]'
        ],
        [
            'field' => 'sex',
            'label' => '性别',
            'rules' => 'in_list[man,woman,secret]'
        ],
        //        [
        //            'field' => 'birthday',
        //            'label' => '生日',
        //            'rules' => ''
        //        ],
    ],
    'comment_delete'      => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'floor',
            'label' => '楼层数',
            'rules' => 'required|greater_than_equal_to[2]|is_natural_no_zero'
        ],
    ],
    'post_comment'        => [
        [
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ],
        [
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        ],
        [
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required'
        ],
        [
            'field' => 'reply_floor',
            'label' => '楼层数',
            'rules' => 'is_natural_no_zero'
        ]
    ],
    'lists'               => [
        [
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ],
        [
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        ]
    ],
    'join_group'          => [
        [
            'field' => 'group_id',
            'label' => '星球名称',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ]
    ],
    'create_group'        => [
        [
            'field' => 'g_name',
            'label' => '星球名称',
            'rules' => 'required|min_length[1]|max_length[80]|regex_match[/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u]|is_string'
        ],
        [
            'field' => 'g_introduction',
            'label' => '星球简介',
            'rules' => 'min_length[1]|max_length[50]'
        ],
        [
            'field' => 'g_image',
            'label' => '星球图片',
            'rules' => 'valid_url'
        ]
    ],
    'message'             => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|is_natural_no_zero'
        ],
        [
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ],
        [
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        ],
        [
            'field' => 'type',
            'label' => '消息分类',
            'rules' => 'in_list[home,apply,group,post]'
        ]
    ],
    'check_token'         => [
        [
            'field' => 'token',
            'label' => '身份信息',
            'rules' => 'required'
        ],
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|is_natural_no_zero'
        ]
    ],
    'send_mail'           => [
        [
            'field' => 'email',
            'label' => '邮箱',
            'rules' => 'required|valid_email'
        ]
    ],
    'login'               => [
        [
            'field' => 'mail',
            'label' => '邮箱',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'password',
            'label' => '密码',
            'rules' => 'required|min_length[8]|max_length[20]'
        ]
    ],
    're_psw'              => [
        [
            'field' => 'password',
            'label' => '密码',
            'rules' => 'required|min_length[8]|max_length[20]'
        ],
        [
            'field' => 'token',
            'label' => '身份信息',
            'rules' => 'required'
        ]
    ],
    'register'                 => [
        [
            'field' => 'name',
            'label' => '昵称',
            'rules' => 'required'
        ],
        [
            'field' => 'mail',
            'label' => '邮箱',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'password',
            'label' => '密码',
            'rules' => 'required|min_length[8]|max_length[60]'
        ]
    ],
    'show_message'        => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required'
        ],
        [
            'field' => 'pn',
            'label' => 'pn',
            'rules' => 'required'
        ],
        [
            'field' => 'm_type',
            'label' => 'm_type',
            'rules' => 'required'
        ]
    ],
    'get_post_base'       => [
        [
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required'
        ]
    ],
    'process_apply'       => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required'
        ],
        [
            'field' => 'm_id',
            'label' => '消息ID',
            'rules' => 'required'
        ],
        [
            'field' => 'mark',
            'label' => '操作码',
            'rules' => 'is_bool'
        ]
    ],
    'private_group'       => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'group_id',
            'label' => 'group_id',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'text',
            'label' => 'text',
            'rules' => 'min_length[1]|max_length[50]'
        ]
    ],
    'posts'               => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'group_id',
            'label' => '星球ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'content',
            'label' => '帖子内容',
            'rules' => 'required|min_length[1]|max_length[49999]'
        ],
        [
            'field' => 'title',
            'label' => '帖子标题',
            'rules' => 'required|min_length[1]|max_length[60]'
        ]
    ],
    'change_psd'               => [
        [
            'field' => 'old_psd',
            'label' => '旧密码',
            'rules' => 'required|min_length[8]|max_length[20]'
        ],
        [
            'field' => 'new_psd',
            'label' => '新密码',
            'rules' => 'required|min_length[8]|max_length[20]'
        ]
    ],
    'edit_post'           => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'content',
            'label' => '帖子内容',
            'rules' => 'required|min_length[1]|max_length[49999]'
        ],
        [
            'field' => 'title',
            'label' => '标题',
            'rules' => 'required|min_length[1]|max_length[60]'
        ]
    ],
    'post_reply'          => [
        [
            'field' => 'user_base_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'post_base_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'comment',
            'label' => '回复内容',
            'rules' => 'required|min_length[1]|max_length[5000]'
        ]
    ],
    'collect_post'        => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
        [
            'field' => 'post_id',
            'label' => '帖子ID',
            'rules' => 'required|min_length[1]|is_natural_no_zero'
        ],
    ],
    'delete_group_member' => [
        [
            'field' => 'group_id',
            'label' => '星球ID',
            'rules' => 'required|min_length[1]'
        ],
        [
            'field' => 'member_id',
            'label' => '成员ID',
            'rules' => 'required|min_length[1]'
        ]
    ],
    'get_create'          => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required'
        ],
    ],
    'get_group_post'      => [
        [
            'field' => 'group_id',
            'label' => '星球ID',
            'rules' => 'required'
        ],
        [
            'field' => 'limit',
            'label' => '每页数量',
            'rules' => 'is_natural_no_zero'
        ],
        [
            'field' => 'offset',
            'label' => '起始值',
            'rules' => 'is_natural'
        ]
    ],
    'search'              => [
        [
            'field' => 'text',
            'label' => 'text',
            'rules' => 'required'
        ],
    ],
    'delete_message'      => [
        [
            'field' => 'user_id',
            'label' => '用户ID',
            'rules' => 'required|is_natural_no_zero'
        ],
        [
            'field' => 'id',
            'label' => '消息ID',
            'rules' => 'required|is_natural_no_zero'
        ],
    ],
    'email'               => [
        [
            'field' => 'emailaddress',
            'label' => 'EmailAddress',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|alpha'
        ],
        [
            'field' => 'title',
            'label' => 'Title',
            'rules' => 'required'
        ],
        [
            'field' => 'message',
            'label' => 'MessageBody',
            'rules' => 'required'
        ]
    ]
];