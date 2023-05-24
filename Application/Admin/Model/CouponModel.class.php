<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/1
 * Time: 14:48
 */

namespace Admin\Model;

use Think\Model;

class CouponModel extends Model{
    protected $_validate = array(
        array('name','require',"兑换券名称必须填写"),
    );

    public function getCouponInfo($id){
        $store_id = session('store_id');
        $couponInfo = $this->where(array('store_id'=>$store_id,'id'=>$id))->select();
        return $couponInfo;
    }

    public function getCouponInfoById($id){
        $store_id = session('store_id');
        $info = $this->where(array('id'=>$id,"store_id"=>$store_id))->select();
        return $info[0];
    }

    public function CouponToRecycleBin($id){
        $store_id = session('store_id');
        $result = $this->where(array('id'=>$id,'store_id'=>$store_id))->save(array('is_delete'=>1));
        return $result;
    }

    public function CouponFromRecycleBin($id){
        $store_id = session('store_id');
        $result = $this->where(array('id'=>$id,'store_id'=>$store_id))->save(array('is_delete'=>0));
        return $result;
    }

    public function DeleteRecycleBin($id){
        $store_id = session('store_id');
        $result = $this->where(array('id'=>$id,'is_delete'=>1,'store_id'=>$store_id))->delete();
        return $result;
    }

}