<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/4
 * Time: 14:01
 */

namespace Admin\Model;


use Think\Model;

class GoodsAttrModel extends Model{
    protected $_validate = array(
        array('attr_name','arrayrequire','商品属性名称不能为空！',0,'callback'),
        array('attr_value','arrayrequire','商品属性值不能为空！',0,'callback'),
        array('attr_price','arrayrequire','商品价格不能为空或0！',0,'callback'),
    );

    public function arrayrequire($a){
        if(is_array($a)){
            foreach($a as $k=>$v){
                if(empty($v)){
                    return false;
                }
            }
            return true;
        }else{
            return false;
        }
    }

    public function getGoodsAttrInfo($goods_id){
        $info = $this->where(array('goods_id'=>$goods_id))->select();
        return $info;
    }

    public function deleteAttrByGoodsAttrId($goods_attr_id){
        return $this->where(array('goods_attr_id'=>$goods_attr_id))->delete();
    }
}