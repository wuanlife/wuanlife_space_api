<?php
/**
 * Created by PhpStorm.
 * User: tacer
 * Date: 2017/12/29
 * Time: 23:51
 */

class Qiniu extends REST_Controller
{
    public function __construct(string $config = 'rest')
    {
        parent::__construct($config);
        // 加载七牛模版
        $this->load->model('qiniu_model');
        // 加载jwt类
        $this->load->library('jwt');
    }

    /**
     * 获取七牛Token
     */
    public function token_get(): void
    {
        // 验证用户权限
        try {
            $this->jwt->decode(
                $this->input->get_request_header('Access-Token', true),
                $this->config->item('encryption_key')
            );
        } catch (Exception $e) {
            $this->response(['error' => '身份信息已失效，请重新获取'], 401);
        }
        // 获取七牛 Token
        $token = $this->qiniu_model->getToken();
        // 返回 Token
        if ($token == 'error') {
            $this->response(['error' => '获取失败'], 401);
        } else {
            $this->response(['uploadToken' => $token]);
        }
    }

}