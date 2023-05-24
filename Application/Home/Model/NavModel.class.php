<?php

/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/16
 * Time: 16:11
 */

namespace Home\Model;

use Think\Model;

class NavModel extends CommonModel
{
    public function getAllInfo() {
        $condition['store_id'] = $this->store_id;
        $result = $this->where($condition)->select();
        return $result;
    }
    
    public function getNavByParentId($id) {
        $condition['store_id'] = $this->store_id;
        $condition['parent_id'] = $id;
        $result = $this->where($condition)->select();
        return $result;
    }
    
    public function getInfoById($id) {
        $condition['id']= $id;
        $condition['store_id'] = $this->store_id;
        $result = $this->where($condition)->select();
        return $result;
    }
}
