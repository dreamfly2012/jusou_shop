<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/10
 * Time: 14:47
 */


namespace Admin\Model;


use Think\Model;

class OrderGoodsModel extends Model{
    public function getGoodsInfoById($order_id){
        $result = $this->where(array('order_id'=>$order_id))->select();
        return $result;
    }

    public function getOrderIdByGoodsCotains($goods_name,$order_store_ids){
        $map['goods_name'] = array('like',"%".$goods_name."%");
        $map['order_id']  = array('in',$order_store_ids);
        $result = $this->where($map)->select();
        return $result;
    }
}