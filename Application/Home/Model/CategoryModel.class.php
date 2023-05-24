<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/1/14
 * Time: 15:44
 */
namespace Home\Model;
use Think\Model;

class CategoryModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCatNameById($id){
        $condition['cat_id'] = $id;
        $condition['store_id'] = $this->store_id;
        $condition['_logic'] = 'AND';
        $result = $this->field('cat_name')->where($condition)->select();
        return $result[0]['cat_name'];
    }

    public function getCategoryByStoreId($store_id){
        $result = $this->where(array('store_id'=>$store_id))->select();
        return $result; 
    }
}