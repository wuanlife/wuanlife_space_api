<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 输入验证类
 */
class Validator {
    private function get_rules(){
        return array(
            'delete_message'=>array(
                'id'=>array(
                    'required'=>true,
                    'min'=>1,
                    'max'=>15
                ),
            ),
            'test'=>array(
                'user_id'=>array(
                    'required'=>true,
                    'min'=>1,
                ),
                'm_id'=>array(
                    'required'=>true,
                    'min'=>1,
                ),
                'mark'=>array(
                    'required'=>true,
                    'min'=>1
                ),
            ),
        );
    }
    public function check($data,$function){
        $re = array();
        $length = NULL;
        $num = count($data);
        for($i=0;$i<$num;$i++){
            $rs[array_keys($data)[$i]] = $this->get_rules()[$function][array_keys($data)[$i]];
            if($rs[array_keys($data)[$i]]['required']==true){
                $re[array_keys($data)[$i]]['required'] =  $this->is_empty($data[array_keys($data)[$i]]);
            }else{
                $re[array_keys($data)[$i]]['required'] = true;
            }
            $type = 4;
            if(!empty($rs[array_keys($data)[$i]]['min'])&&!empty($rs[array_keys($data)[$i]]['max'])){
                $type = 3;
            }elseif(!empty($rs[array_keys($data)[$i]]['min'])&&empty($rs[array_keys($data)[$i]]['max'])){
                $type = 1;
                //$length = '长度不小于'.$rs[array_keys($data)[$i]]['min'];
            }elseif(empty($rs[array_keys($data)[$i]]['min'])&&!empty($rs[array_keys($data)[$i]]['max'])){
                $type = 2;
                //$length = '长度不大于'.$rs[array_keys($data)[$i]]['max'];
            }
            if($i==3){
                return $type;
            }
            $re[array_keys($data)[$i]]['length'] = $this->length($data[array_keys($data)[$i]],$type,@$rs[array_keys($data)[$i]]['min'],@$rs[array_keys($data)[$i]]['max']);
        }
        //return $re;
        $re = $this->show_error($re);
        if(is_array($re)){
            $re = '参数'.$re[0].'长度应不小于 '.@$rs[array_keys($data)[$re[2]]]['min'].' 不大于 '.@$rs[array_keys($data)[$re[2]]]['max'];
        }
        return $re;

    }
    public function show_error($data){
        $num = count($data);
        for($i=0;$i<$num;$i++){
            //var_dump($data[array_keys($data)[$i]]);
            if($data[array_keys($data)[$i]]['required']==false){
                $rs = array_keys($data)[$i].'为必须参数';
                return $rs;
                break;
            }
            if($data[array_keys($data)[$i]]['length']==false){
                $rs[2] = $i;
                $rs[0] = array_keys($data)[$i];
                return $rs;
                break;
            }
            continue;
        }
        return true;
    }
    private function is_empty($str){
        $str = trim($str);
        return !empty($str) ? true : false;
    }
    private function is_email($str){
        if(!$this->is_empty($str)) return false;
        return preg_match("/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i",$str) ? true : false;
    }
    private function length($str,$type=3,$min=0,$max=0,$charset = 'utf-8'){
        if(!$this->is_empty($str)) return false;
        $len = mb_strlen($str,$charset);
        switch($type){
            case 1: //只匹配最小值
                return ($len >= $min) ? true : false;
                break;
            case 2: //只匹配最大值
                return ($max >= $len) ? true : false;
                break;
            case 3: //min <= $str <= max
                return (($min <= $len) && ($len <= $max)) ? true : false;
                break;
            default:
                return true;
        }
    }

}