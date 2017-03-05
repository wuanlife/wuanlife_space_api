<?php



class Group_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function create($data){

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




}