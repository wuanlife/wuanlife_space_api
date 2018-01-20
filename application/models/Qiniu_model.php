<?php
/**
 * Created by PhpStorm.
 * User: tacer
 * Date: 2017/12/30
 * Time: 0:01
 */

class Qiniu_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 加载七牛配置文件
     * @return array
     */
    private function loadConfig(): array
    {
        //加载七牛配置文件
        $this->config->load('qiniu');
        return
            [
                'accessKey' => $this->config->item('accessKey'),
                'secretKey' => $this->config->item('secretKey'),
                'bucket'    => $this->config->item('bucket')
            ];
    }

    /**
     * 获取七牛上传 Token
     * @return string
     */
    public function getToken(): string
    {
        // 加载七牛文件
        try {
            require APPPATH . 'libraries/Autoload.php';
        }catch (Exception $e){
            return 'error';
        }
        // 加载配置文件
        $config = $this->loadConfig();
        // 初始化签权对象
        $auth = new Qiniu\Auth($config['accessKey'], $config['secretKey']);
        // 生成上传Token
        $token = $auth->uploadToken($config['bucket']);

        return $token;
    }

}