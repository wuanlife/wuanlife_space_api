<?php
/**
 * Created by PhpStorm.
 * User: tacer
 * Date: 2017/12/30
 * Time: 0:53
 */

spl_autoload_register(function ($class) {
    $address = APPPATH . 'libraries/' . $class . '.php';
    // 如果文件存在，则 require 该文件，否则抛出一个异常
    if (file_exists($address)) {
        require_once $address;
    }else{
        throw new \Exception('引用的文件不存在！');
    }
});

require APPPATH . 'libraries/Qiniu/functions.php';
