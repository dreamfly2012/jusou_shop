<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/1/13
 * Time: 14:53
 */

namespace Home\Model;

use Think\Model;

class GoodsModel extends CommonModel{
    public function getAllGoodsByStoreId($store_id){
        $result = $this->where(array('store_id'=>$store_id,'is_delete'=>0))->select();
        return $result;
    }

    public function getGoodsById($goods_id){
    	$result = $this->where(array('goods_id'=>$goods_id))->find();
    	return $result;
    }

    public function getPriceInfo($goods_id){
    	$result = $this->field('market_price,shop_price,promote_price,is_promote')->where(array('goods_id'=>$goods_id))->select();
    	return $result[0];
    }

    public function getStoreIdByGoodsId($goods_id){
        $result = $this->field('store_id')->where(array('goods_id'=>$goods_id))->find();
        return $result['store_id'];
    }

    public function getGoodsByCatId($cat_id){
        $result = $this->where(array('store_id'=>$this->store_id,'cat_id'=>$cat_id))->select();
        return $result;
    }

    
}