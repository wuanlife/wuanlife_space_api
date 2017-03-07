<?php



class Common_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }


    /*
  * 判断是否为私密
  */
    public function judgePrivate($private){
        if($private==1){
            $private=1;
        }else{
            $private=0;
        }

        return $private;
    }

    /*
    判断用户是否在线(调用前端接口)
     */
    public function judgeUserOnline($user_id){
        $data1 = array ('userid' => $user_id);
        $data1 = http_build_query($data1);
        $RootDIR = dirname(__FILE__);
        $path=$RootDIR."/../../Public/init.php";
        require_once $path;
        $url=DI()->config->get('sys.url');


        $opts = array (
            'http' => array (
                'method' => 'POST',
                'header'=> "Content-type: application/x-www-form-urlencoded",
                'content' => $data1
            )
        );
        $context = stream_context_create($opts);
        $html = file_get_contents($url, false, $context);
        return $html;
    }
    /*
     * 判断星球是否有头像，若没有给默认头像
     */
    public function judge_image_exist($lists){
        for($i=0;$i<count($lists);$i++){
            if(empty($lists[$i]["g_image"])||$lists[$i]["g_image"]==null){
                $lists[$i]["g_image"]='http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100';
            }
        }
        return $lists;
    }


}