<?php
$config = array(
    'error_prefix' =>'',
    'error_suffix' =>'',
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
    'check_new_info' =>array(
        array(
            'field' => 'id',
            'label' => 'user_id',
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