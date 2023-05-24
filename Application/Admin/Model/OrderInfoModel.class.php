<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/10
 * Time: 10:29
 */

namespace Admin\Model;

use Think\Model;

class OrderInfoModel extends Model{
    /**protected $_auto = array (
        array('add_time','convertDate',3,'callback'),
    );*/

    public function convertDate($time){
        dump($time);
        //TODO: 转换时间callback
    }

    public function getOrderInfo(){
        $store_id = session('store_id');
        if($store_id=='admin'){
            $result = $this->select();
        }else{
            $result = $this->where(array('store_id'=>$store_id))->select();
        }
        return $result;
    }

    public function getOrderInfoById($order_id){
        $store_id = session('store_id');
        $result = $this->where(array('store_id'=>$store_id,'order_id'=>$order_id))->select();
        return $result[0];
    }

    public function getOrderIdBySn($order_sn){
        $store_id = session('store_id');
        $result = $this->field('order_id')->where(array('store_id'=>$store_id,'order_sn'=>$order_sn))->select();
        return $result[0]['order_id'];
    }

    public function getOrderAmountById($order_id){
        $store_id = session('store_id');
        $result = $this->field('goods_amount,tax,shipping_fee,insure_fee,pay_fee,pack_fee,card_fee,money_paid,surplus,integral_money,bonus,discount')->where(array('store_id'=>$store_id,'order_id'=>$order_id))->select();
        $own = $result[0]['money_paid'] + $result[0]['surplus'] + $result[0]['integral_money'] + $result[0]['bonus'] + $result[0]['discount'];
        $pay = $result[0]['goods_amount'] + $result[0]['tax'] + $result[0]['shipping_fee'] + $result[0]['insure_fee'] + $result[0]['pay_fee'] + $result[0]['pack_fee'] + $result[0]['card_fee'];

        return $own - $pay;
    }

    public function getOrderInfoByUserIdContains($user_ids,$order_store_ids){
        $map['user_id'] = array('in',$user_ids);
        $map['order_id']  = array('in',$order_store_ids);
        $result = $this->field('order_id')->where($map)->select();
        return $result;
    }

    public function getOrderInfoByOrderIds($order_ids){
        $map['order_id']  = array('in',$order_ids);
        $result = $this->where($map)->select();
        return $result;
    }

}