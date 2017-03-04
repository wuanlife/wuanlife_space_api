<?php

/**
 * @author: Chac
 * @since: 2017/3/2 16:09
 */
class Blog extends CI_Controller
{
    public function response($data,$ret=200,$msg=null){
        $response=array('ret'=>$ret,'data'=>$data,'msg'=>$msg);
        $this->output
            ->set_status_header($ret)
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: 0')
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response));
    }

    public function index(){
        $data=array(
            'hello'=>'world',
        );
        $this->response($data);

    }

}