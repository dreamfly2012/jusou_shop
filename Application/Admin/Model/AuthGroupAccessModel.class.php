<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/18
 * Time: 10:50
 */

namespace Admin\Model;

use Think\Model;

class AuthGroupAccessModel extends Model{
    public function deleteRoleFromGroup($uid,$group_id){
        $result = $this->where(array('uid'=>$uid,'group_id'=>$group_id))->delete();
        return $result;
    }
}