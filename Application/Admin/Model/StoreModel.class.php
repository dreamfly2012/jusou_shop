<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/17
 * Time: 10:47
 */

namespace Admin\Model;

use Think\Model;

class StoreModel extends Model{
    protected $_validate = array(
        array('display_name','require','商店显示名称必填'), //默认情况下用正则进行验证
    );

    public function getStoreInfo($store_id){
        $result = $this->where(array('store_id'=>$store_id))->select();
        return $result[0];
    }


}