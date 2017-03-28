<?php
$config = array(
    'error_prefix' =>'',
    'error_suffix' =>'',
    'send_mail'=>array(
        array(
            'field' => 'email',
            'label' => 'user_email',
            'rules' => 'required|valid_email'
        )
    ),
    'login'=>array(
        array(
            'field' => 'email',
            'label' => 'user_email',
            'rules' => 'required|valid_email'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|min_length[6]'
        )
    ),
    're_psw'=>array(
        array(
            'field' => 'user_id',
            'label' => 'user_id',
            'rules' => 'required'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|min_length[6]'
        ),
        array(
            'field' => 'psw',
            'label' => 'psw',
            'rules' => 'required|min_length[6]'
        ),
    ),
    'reg'=>array(
        array(
            'field' => 'nickname',
            'label' => 'user_name',
            'rules' => 'required'
        ),
        array(
            'field' => 'email',
            'label' => 'user_email',
            'rules' => 'required|valid_email'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|min_length[6]'
        ),
        array(
            'field' => 'code',
            'label' => 'i_code',
            'rules' => 'required'
        ),
    ),
    'show_message' => array(
        array(
            'field' => 'user_id',
            'label' => 'user_id',
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
            'label' => 'user_id',
            'rules' => 'required'
        ),
        array(
            'field' => 'm_id',
            'label' => 'm_id',
            'rules' => 'required'
        ),
        array(
            'field' => 'mark',
            'label' => 'mark',
            'rules' => 'required'
        )
    ),
    'private_group' =>array(
        array(
            'field' => 'user_id',
            'label' => 'user_id',
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
            'label' => 'user_id',
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
    'edit_post' =>array(
        array(
            'field' => 'user_id',
            'label' => 'user_id',
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
        ),
        array(
            'field' => 'p_title',
            'label' => 'p_title',
            'rules' => 'required|min_length[1]|max_length[60]'
        )
    ),
    'post_reply' =>array(
        array(
            'field' => 'user_id',
            'label' => 'user_id',
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
    'delete_group_member' =>array(
        array(
            'field' => 'user_id',
            'label' => 'user_id',
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
            'label' => 'user_id',
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
            'field' => 'id',
            'label' => 'm_id',
            'rules' => 'required'
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