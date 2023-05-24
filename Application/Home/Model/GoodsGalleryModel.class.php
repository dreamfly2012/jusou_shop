<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/1/14
 * Time: 15:44
 */
namespace Home\Model;

use Think\Model;

class GoodsGalleryModel extends Model{
    public function getGoodsGalleryById($id){
        $result = $this->where(array('goods_id'=>$id))->select();
        return $result;
    }
}