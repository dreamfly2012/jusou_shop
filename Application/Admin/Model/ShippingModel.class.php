<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/12
 * Time: 15:49
 */

namespace Admin\Model;

use Think\Model;

class ShippingModel extends Model{
    public function getShippingNameById($shipping_id){
        $result = $this->field('shipping_name')->where(array('shipping_id'=>$shipping_id))->select();
        return $result[0]['shipping_name'];
    }

}