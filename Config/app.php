<?php
/**
 * 请在下面放置任何您需要的应用配置
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),
    /**
     * 云上传引擎,支持local,oss,upyun
     */
    'UCloudEngine' => 'local',
    'PHPMailer' => array(
        'email' => array(
			'smtpsecure' => 'ssl',
            'host' => 'smtp.163.com',
			'port' => '465',
            'username' => 'wuanlife@163.com',
            'password' => 'wuan1234',
            'from' => 'wuanlife@163.com',
            'fromName' => '午安网团队',
            'sign' => '<br/><br/>请不要回复此邮件，谢谢！<br/><br/>-- 午安网团队敬上 ',
        ),
    ),
);