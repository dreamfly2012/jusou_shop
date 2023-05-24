<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/1
 * Time: 13:13
 */

namespace Admin\Model;
use Think\Model;

class CategoryModel extends Model{
    public function getChildCategory($parent,$store_id){
        $children = $this->field('cat_id,cat_name')->where(array('parent_id'=>$parent,'store_id'=>$store_id))->select();
        return $children;
    }

    public function hasParent(){
        $result = $this->field('cat_id,cat_name')->where(array('parent_id'=>$_self))->select();
        return $result;
    }

    public function deleteByStoreId($store_id){
    	$result = $this->where(array('store_id'=>$store_id))->delete();
    	return $result;
    }
}