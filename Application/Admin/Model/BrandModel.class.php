<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/1
 * Time: 14:48
 */

namespace Admin\Model;

use Think\Model;

class BrandModel extends Model{
    protected $_validate = array(
        array('brand_name','require',"品牌名称必须填写"),
    );

    public function getBrandInfo($brand_id){
        $store_id = session('store_id');
        $brandsInfo = $this->where(array('store_id'=>$store_id,'brand_id'=>$brand_id))->select();
        return $brandsInfo;
    }

    public function getBrandInfoById($brand_id){
        $store_id = session('store_id');
        $info = $this->where(array('brand_id'=>$brand_id,"store_id"=>$store_id))->select();
        return $info[0];
    }

    public function getBrandLogo($brand_id){
        $store_id = session('store_id');
        $logo = $this->field('brand_logo')->where(array('brand_id'=>$brand_id,'store_id'=>$store_id))->select();
        return $logo[0]['brand_logo'];
    }

    public function BrandToRecycleBin($brand_id){
        $store_id = session('store_id');
        $result = $this->where(array('brand_id'=>$brand_id,'store_id'=>$store_id))->save(array('is_delete'=>1));
        return $result;
    }

    public function BrandFromRecycleBin($brand_id){
        $store_id = session('store_id');
        $result = $this->where(array('brand_id'=>$brand_id,'store_id'=>$store_id))->save(array('is_delete'=>0));
        return $result;
    }

    public function DeleteRecycleBin($brand_id){
        $store_id = session('store_id');
        $result = $this->where(array('brand_id'=>$brand_id,'is_delete'=>1,'store_id'=>$store_id))->delete();
        return $result;
    }

}