<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/5
 * Time: 11:26
 */

namespace Admin\Model;


use Think\Model;

class GoodsGalleryModel extends  Model{
    public function deleteGalleryByImgId($img_id){
        return $this->where(array('img_id'=>$img_id))->delete();
    }

    public function getGalleryInfo($goods_id){
        $info = $this->where(array('goods_id'=>$goods_id))->select();
        return $info;
    }

    public function getImgUrlById($img_id){
        $img_url = $this->field('img_url')->where(array('img_id'=>$img_id))->select();
        return $img_url[0]['img_url'];
    }

    public function getThumbUrlById($img_id){
        $thumb_url = $this->field('thumb_url')->where(array('img_id'=>$img_id))->select();
        return $thumb_url[0]['thumb_url'];
    }
} 