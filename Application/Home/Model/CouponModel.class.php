<?php

namespace Home\Model;

class CouponModel extends Model
{
	protected $_validate = array(
        array('coupon_code','require','兑换码必须填写'),
    );

    public function getInfoByCoupon($coupon_code){
    	$result = $this->where(array('coupon_code'=>$coupon_code))->select();
    	return $result[0];

    }
}