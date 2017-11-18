<?php
/**
 * Created by PhpStorm.
 * User: 小超
 * Time: 2017/08/13 16:38
 */
function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/' . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');
require_once  __DIR__ . '/Qiniu/functions.php';