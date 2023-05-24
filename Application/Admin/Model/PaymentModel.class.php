<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/10
 * Time: 15:43
 */

namespace Admin\Model;

use Think\Model;

class PaymentModel extends Model{
    public function getPaymentInfoById($pay_id){
        $result = $this->where(array('pay_id'=>$pay_id))->select();
        return $result[0];
    }
}