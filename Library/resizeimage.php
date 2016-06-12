<?php
    /*
     * desc: Resize Image(png, jpg, gif)
     * author: 十年后的卢哥哥(http://www.cnblogs.com/lurenjiashuo/)
     * date: 2014.11.13
     * base from: http://www.oschina.net/code/snippet_5189_2491
     */
    class ResizeImage {
        //图片类型
        private $type;
        //实际宽度
        private $width;
        //实际高度
        private $height;
        //改变后的宽度
        private $resize_width;
        //改变后的高度
        private $resize_height;
        //是否裁图
        private $cut;
        //源图象
        private $srcimg;
        //目标图象地址
        private $dstimg;
        //临时创建的图象
        private $im;

        function __construct($imgPath, $width, $height, $isCut, $savePath) {
            $this->srcimg = $imgPath;
            $this->resize_width = $width;
            $this->resize_height = $height;
            $this->cut = $isCut;
            //图片的类型

            $this->type = strtolower(substr(strrchr($this->srcimg,"."),1));

            //初始化图象
            $this->initi_img();
            //目标图象地址
            $this -> dst_img($savePath);
            //--
            $this->width = imagesx($this->im);
            $this->height = imagesy($this->im);
            //生成图象
            $this->newimg();
            ImageDestroy ($this->im);
        }

        private function newimg() {
            //改变后的图象的比例
            $resize_ratio = ($this->resize_width)/($this->resize_height);
            //实际图象的比例
            $ratio = ($this->width)/($this->height);
            if($this->cut) {
                //裁图
                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);
                if($this->type=="png") {
                    imagefill($newimg, 0, 0, imagecolorallocatealpha($newimg, 0, 0, 0, 127));
                }
                if($ratio>=$resize_ratio) {
                    //高度优先
                    imagecopyresampled($newimg, $this->im, 0, 0,(($this->width)/500),0, $this->resize_width,$this->resize_height, (($this->height)*$resize_ratio), $this->height);
                } else {
                    //宽度优先
                    imagecopyresampled($newimg, $this->im, 0, 0,0,(($this->height)/500), $this->resize_width, $this->resize_height, $this->width, (($this->width)/$resize_ratio));
                }
            } else {
                //不裁图
                if($ratio>=$resize_ratio) {
                    $newimg = imagecreatetruecolor($this->resize_width,($this->resize_width)/$ratio);
                    if($this->type=="png") {
                        imagefill($newimg, 0, 0, imagecolorallocatealpha($newimg, 0, 0, 0, 127));
                    }
                    imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, ($this->resize_width)/$ratio, $this->width, $this->height);
                } else {
                    $newimg = imagecreatetruecolor(($this->resize_height)*$ratio,$this->resize_height);
                    if($this->type=="png") {
                        imagefill($newimg, 0, 0, imagecolorallocatealpha($newimg, 0, 0, 0, 127));
                    }
                    imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, ($this->resize_height)*$ratio, $this->resize_height, $this->width, $this->height);
                }
            }
            if($this->type=="png") {
                imagesavealpha($newimg, true);
                imagepng ($newimg,$this->dstimg);
            } else {
                imagejpeg ($newimg,$this->dstimg);
            }
        }

        //初始化图象
        private function initi_img() {
            if($this->type=="jpg") {
                $this->im = imagecreatefromjpeg($this->srcimg);
            }
            if($this->type=="gif") {
                $this->im = imagecreatefromgif($this->srcimg);
            }
            if($this->type=="png") {
                $this->im = imagecreatefrompng($this->srcimg);
            }
        }

        //图象目标地址
        private function dst_img($dstpath) {
            $full_length  = strlen($this->srcimg);

            $type_length  = strlen($this->type);
            $name_length  = $full_length-$type_length;


            $name         = substr($this->srcimg,0,$name_length-1);
            $this->dstimg = $dstpath;
        }
    }
?>