<?php



class Group extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Group_model');
        $this->load->model('Common_model');
        $this->load->helper('url_helper');
    }
    public function response($data,$ret=200,$msg=null){
        $response=array('ret'=>$ret,'data'=>$data,'msg'=>$msg);
        $this->output
            ->set_status_header($ret)
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: 0')
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))
            ->_display();
        exit;
    }



    public function create($user_id,$g_name,$g_image,$g_introduction,$private){
        $private=$this->Common_model->judgePrivate($private);
        $data=array(
            'user_id'=>$user_id,
            'g_name'=>$g_name,
            'g_image'=>$g_image,
            'g_introduction'=>$g_introduction,
            'private'=>$private,
        );
        $msg=null;
        $re=$this->Group_model->create($data);
        $this->response($re,200,$msg);

    }







}