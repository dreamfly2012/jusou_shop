<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/17
 * Time: 11:48
 */

namespace Home\Model;

use Think\Model;

class BrandModel extends Model{
    public function getBrandInfo($store_id){
        $brandsInfo = $this->where(array('store_id'=>$store_id))->select();
        return $brandsInfo;
    }

    public function getBrandInfoById($brand_id){
        $info = $this->where(array('brand_id'=>$brand_id))->select();
        return $info[0];
    }

    public function getBrandInfoByIds($ids){
    	$map['brand_id']  = array('in',$ids);
    	$info = $this->where($map)->select();
    	return $info;
    }

    public function getIdsByStoreId($store_id){
    	$info = $this->field('brand_id')->where(array('store_id'=>$store_id))->select();
    	return $info;
    }
}