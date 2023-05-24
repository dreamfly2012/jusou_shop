<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/11/27
 * Time: 11:24
 */

namespace Admin\Model;

use Think\Model;

class GoodsModel extends Model{
    protected $_validate = array(
        array('goods_name','require','商品名称不能为空！'),
        array('cat_id','require','商品分分类不能为空！'),
        array('is_promote','compareDate','促销开始日期不能小于结束日期',0,'callback',3),
        array('goods_weight','/^(([0-9]+.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*.[0-9]+)|([0-9]*[1-9][0-9]*))$/','商品重量必须是数字',2),
        array('promote_price','/^(([0-9]+.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*.[0-9]+)|([0-9]*[1-9][0-9]*))$/','促销价格必须是正的浮点数',2),
        array('market_price','/^(([0-9]+.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*.[0-9]+)|([0-9]*[1-9][0-9]*))$/','市场价格必须是正的浮点数',2),
        array('shop_price','/^(([0-9]+.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*.[0-9]+)|([0-9]*[1-9][0-9]*))$/','本店售价价格必须是正的浮点数',2),
        array('warn_number','/^[0-9]*[1-9][0-9]*$/','库存警告必须是数字',2),
        array('goods_number','/^[0-9]*[1-9][0-9]*$/','库存必须是正整数',2),
        //TODO : 验证规则不够完善还有些字段需要仔细考虑
    );

    public function compareDate(){
        $promote_start_date = I('post.promote_start_date',null);

        $promote_end_date = I('post.promote_end_date',null);

        $str_promote_start_date = strtotime($promote_start_date);

        $str_promote_end_date = strtotime($promote_end_date);
        
        if(!is_null($promote_start_date) && !is_null($promote_end_date)){
            return ($str_promote_end_date-$str_promote_start_date)>0 ? true : false;
        }else{
            return true;
        }
    }
    public function getAllGoods(){
        $store_id = session('store_id');
        $result = $this->where(array('store_id'=>$store_id))->select();
        return $result;
    }

    public function getGoodsNameById($goods_id){
        $result = $this->field('goods_name')->where(array('goods_id'=>$goods_id))->select();
        return $result[0]['goods_name'];
    }

    public function getGoodsImgById($goods_id){
        $result = $this->field('goods_img')->where(array('goods_id'=>$goods_id))->select();
        return $result[0]['goods_img'];
    }

    public function getGoodsThumbById($goods_id){
        $result = $this->field('goods_thumb')->where(array('goods_id'=>$goods_id))->select();
        return $result[0]['goods_thumb'];
    }

    public function GoodsToRecycleBin($goods_id){
        $result = $this->where(array('goods_id'=>$goods_id))->save(array('is_delete'=>1));
        return $result;
    }

    public function GoodsFromRecycleBin($goods_id){
        $result = $this->where(array('goods_id'=>$goods_id))->save(array('is_delete'=>0));
        return $result;
    }

    
    public function DeleteRecycleBin($goods_id){
        $result = $this->where(array('goods_id'=>$goods_id,'is_delete'=>1))->delete();
        return $result;
    }

    public function UpdateGoodsOnsale($goods_id,$is_on_sale){
        $result = $this->where(array('goods_id'=>$goods_id))->save(array('is_on_sale'=>$is_on_sale));
        return $result;
    }

    public function getGoodsInfo($id){
        $goodsInfo = $this->where(array('goods_id'=>$id))->select();
        return $goodsInfo[0];
    }

    public function checkExistGoodsSn($goods_sn){
        $res = $this->field('goods_sn')->where(array('goods_sn'=>$goods_sn))->select();
        if($res){
            return true;
        }else{
            return false;
        }
    }
}