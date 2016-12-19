<?php
/**
 * 以下配置为系统级的配置，通常放置不同环境下的不同配置
 */

return array(
	/**
	 * 默认环境配置
	 */
	'debug' => false,

	/**
	 * MC缓存服务器参考配置
	 */
	 'mc' => array(
        'host' => '127.0.0.1',
        'port' => 11211,
	 ),

    /**
     * 加密
     */
    'crypt' => array(
        'mcrypt_iv' => '12345678',      //8位
    ),
    /**
     * 判断用户是否在线url
     * 测试地址：'url'=>'http://dev.wuanlife.com/news'
     * 正式地址：url=>'http://wuanla.com/news'
     */
    //'url'=>'http://wuanla.com/news',
    'url'=>'http://dev.wuanlife.com/news',
);
