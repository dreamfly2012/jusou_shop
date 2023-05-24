<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/12
 * Time: 10:41
 */

namespace Admin\Model;

use Think\Model;

class AdminAddressModel extends Model{

    //TODO：加上user_id，防止注入攻击，修改他人的地址
    public function getAddressInfoById($address_id){
        $result = $this->where(array('address_id'=>$address_id))->select();
        return $result[0];
    }

    public function addressSetDefault($address_id,$type){
        $user_id = session('user_id');
        $this->where(array('user_id'=>$user_id,'type'=>$type))->save(array('is_default'=>1));
        $this->where(array('address_id'=>$address_id))->save(array('is_default'=>1));
    }

    public function getDefaultAddressIdByUserId($user_id,$type){
        $result = $this->field('address_id')->where(array('user_id'=>$user_id,'is_default'=>1,'type'=>$type))->select();
        return $result[0]['address_id'];
    }

    public function getDeliverAddressByUserId($user_id){
        $result = $this->field('address_id,address_name')->where(array('user_id'=>$user_id,'type'=>'deliver'))->select();
        return $result;
    }


    public function getRefundAddressByUserId($user_id){
        $result = $this->field('address_id,address_name')->where(array('user_id'=>$user_id,'type'=>'refund'))->select();
        return $result;
    }

    public function getProvinceIdById($address_id){
        $result = $this->field('province')->where(array('address_id'=>$address_id))->select();
        return $result[0]['province'];
    }
    public function getCityIdById($address_id){
        $result = $this->field('city')->where(array('address_id'=>$address_id))->select();
        return $result[0]['city'];
    }
    public function getDistrictIdById($address_id){
        $result = $this->field('district')->where(array('address_id'=>$address_id))->select();
        return $result[0]['district'];
    }

    public function getAddressNameById($address_id){
        $result = $this->field('address')->where(array('address_id'=>$address_id))->select();
        return $result[0]['address'];
    }
}